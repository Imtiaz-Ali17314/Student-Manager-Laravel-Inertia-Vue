<?php

namespace App\Http\Controllers;

use App\Http\Resources\StudentResource;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalStudents = Student::count();
        $totalClasses = Classes::count();
        $totalSections = Section::count();

        $recentStudents = StudentResource::collection(
            Student::latest()->take(5)->get()
        );

        $classDistribution = Classes::withCount('students')
            ->get()
            ->map(fn ($class) => [
                'id' => $class->id,
                'name' => $class->name,
                'students_count' => $class->students_count,
            ]);

        return inertia('Dashboard', [
            'stats' => [
                'total_students' => $totalStudents,
                'total_classes' => $totalClasses,
                'total_sections' => $totalSections,
            ],
            'recent_students' => $recentStudents,
            'class_distribution' => $classDistribution,
        ]);
    }
}
