<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Users, 
    School, 
    Layers, 
    UserPlus, 
    ArrowRight, 
    Eye,
    TrendingUp,
    GraduationCap
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import studentsRoute from '@/routes/students';

export interface ClassResourceData {
    id: number;
    name: string;
}

export interface SectionResourceData {
    id: number;
    name: string;
}

export interface StudentResourceData {
    id: number;
    name: string;
    email: string;
    class_id?: ClassResourceData | null;
    section_id?: SectionResourceData | null;
    created_at: string;
}

export interface ClassDistributionItem {
    id: number;
    name: string;
    students_count: number;
}

const props = defineProps<{
    stats: {
        total_students: number;
        total_classes: number;
        total_sections: number;
    };
    recent_students: StudentResourceData[] | { data: StudentResourceData[] };
    class_distribution: ClassDistributionItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    },
});

// Normalize recent students array
const recentStudentList = computed<StudentResourceData[]>(() => {
    if (!props.recent_students) return [];
    return Array.isArray(props.recent_students)
        ? props.recent_students
        : props.recent_students.data || [];
});

// Helper for Initials
function getInitials(name: string): string {
    if (!name) return 'ST';
    return name
        .split(' ')
        .map((part) => part[0])
        .filter(Boolean)
        .slice(0, 2)
        .join('')
        .toUpperCase();
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Dashboard Top Banner / Welcome Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-foreground">
                    Welcome to Student Manager
                </h1>
                <p class="text-sm text-muted-foreground">
                    Overview of student enrollments, class distribution, and quick operations.
                </p>
            </div>

            <!-- Quick Action Shortcuts -->
            <div class="flex flex-wrap items-center gap-2">
                <Button as-child variant="default" class="gap-2 shadow-xs">
                    <Link :href="studentsRoute.create()">
                        <UserPlus class="h-4 w-4" />
                        <span>Add Student</span>
                    </Link>
                </Button>

                <Button as-child variant="outline" class="gap-2">
                    <Link :href="studentsRoute.index()">
                        <span>View All Students</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Metric Cards Grid -->
        <div class="grid gap-4 sm:grid-cols-3">
            <!-- Total Students Card -->
            <Card class="border-sidebar-border/70 shadow-xs relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Total Students
                    </CardTitle>
                    <div class="p-2 rounded-lg bg-primary/10 text-primary">
                        <Users class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-foreground">
                        {{ stats.total_students }}
                    </div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                        <TrendingUp class="h-3.5 w-3.5" />
                        <span>Active Enrolled Students</span>
                    </div>
                </CardContent>
            </Card>

            <!-- Total Classes Card -->
            <Card class="border-sidebar-border/70 shadow-xs relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Active Classes
                    </CardTitle>
                    <div class="p-2 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        <School class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-foreground">
                        {{ stats.total_classes }}
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Configured academic grades
                    </p>
                </CardContent>
            </Card>

            <!-- Total Sections Card -->
            <Card class="border-sidebar-border/70 shadow-xs relative overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Active Sections
                    </CardTitle>
                    <div class="p-2 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <Layers class="h-4 w-4" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-foreground">
                        {{ stats.total_sections }}
                    </div>
                    <p class="text-xs text-muted-foreground mt-1">
                        Class subsections available
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid gap-6 md:grid-cols-3">
            <!-- Recent Enrolled Students (2 Columns) -->
            <Card class="md:col-span-2 border-sidebar-border/70 shadow-xs overflow-hidden">
                <CardHeader class="flex flex-row items-center justify-between">
                    <div>
                        <CardTitle class="text-lg">Recent Students</CardTitle>
                        <CardDescription>
                            Top 5 latest registered students in the system
                        </CardDescription>
                    </div>
                    <Button as-child variant="ghost" size="sm" class="gap-1 text-xs">
                        <Link :href="studentsRoute.index()">
                            <span>View All</span>
                            <ArrowRight class="h-3.5 w-3.5" />
                        </Link>
                    </Button>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-y border-sidebar-border/70">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Student</th>
                                    <th scope="col" class="px-6 py-3">Class & Section</th>
                                    <th scope="col" class="px-6 py-3">Joined</th>
                                    <th scope="col" class="px-6 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sidebar-border/50">
                                <template v-if="recentStudentList.length > 0">
                                    <tr 
                                        v-for="student in recentStudentList" 
                                        :key="student.id"
                                        class="transition-colors hover:bg-muted/40"
                                    >
                                        <!-- Student Name & Avatar -->
                                        <td class="px-6 py-3.5 font-medium text-foreground">
                                            <div class="flex items-center gap-3">
                                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs border border-primary/20">
                                                    {{ getInitials(student.name) }}
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-foreground leading-tight">{{ student.name }}</div>
                                                    <div class="text-xs text-muted-foreground">{{ student.email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Class & Section -->
                                        <td class="px-6 py-3.5">
                                            <div class="flex items-center gap-1.5 flex-wrap">
                                                <Badge 
                                                    v-if="student.class_id?.name" 
                                                    variant="outline" 
                                                    class="text-xs py-0 bg-blue-50/50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-800"
                                                >
                                                    {{ student.class_id.name }}
                                                </Badge>
                                                <Badge 
                                                    v-if="student.section_id?.name" 
                                                    variant="secondary" 
                                                    class="text-xs py-0 bg-emerald-50/50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800"
                                                >
                                                    {{ student.section_id.name }}
                                                </Badge>
                                            </div>
                                        </td>

                                        <!-- Date -->
                                        <td class="px-6 py-3.5 text-xs text-muted-foreground whitespace-nowrap">
                                            {{ student.created_at }}
                                        </td>

                                        <!-- Action -->
                                        <td class="px-6 py-3.5 text-right">
                                            <Button as-child variant="ghost" size="icon" class="h-7 w-7">
                                                <Link :href="studentsRoute.show(student.id)" title="View Details">
                                                    <Eye class="h-4 w-4 text-muted-foreground" />
                                                </Link>
                                            </Button>
                                        </td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-muted-foreground">
                                            No students registered yet. Click "Add Student" to create your first record.
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>

            <!-- Class Distribution Widget (1 Column) -->
            <Card class="border-sidebar-border/70 shadow-xs flex flex-col">
                <CardHeader>
                    <CardTitle class="text-lg">Class Distribution</CardTitle>
                    <CardDescription>
                        Number of students enrolled in each class
                    </CardDescription>
                </CardHeader>
                <CardContent class="flex-1 space-y-4">
                    <template v-if="class_distribution.length > 0">
                        <div 
                            v-for="item in class_distribution" 
                            :key="item.id"
                            class="space-y-1.5"
                        >
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-foreground flex items-center gap-1.5">
                                    <GraduationCap class="h-4 w-4 text-muted-foreground" />
                                    {{ item.name }}
                                </span>
                                <span class="text-xs font-semibold text-muted-foreground">
                                    {{ item.students_count }} {{ item.students_count === 1 ? 'Student' : 'Students' }}
                                </span>
                            </div>

                            <!-- Visual Bar -->
                            <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                                <div 
                                    class="h-full bg-primary rounded-full transition-all duration-300"
                                    :style="{ 
                                        width: stats.total_students > 0 
                                            ? Math.min(100, Math.max(5, (item.students_count / stats.total_students) * 100)) + '%' 
                                            : '0%' 
                                    }"
                                ></div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div class="py-6 text-center text-sm text-muted-foreground">
                            No classes defined.
                        </div>
                    </template>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
