<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Pencil,
    Trash2,
    Mail,
    Calendar,
    School,
    Layers,
    User,
    ShieldCheck,
    AlertTriangle,
    Loader2,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { dashboard } from '@/routes';
import studentsRoute from '@/routes/students';

export interface ClassResourceData {
    id: number;
    name: string;
    created_at?: string;
    updated_at?: string;
}

export interface SectionResourceData {
    id: number;
    name: string;
    class_id?: number;
    created_at?: string;
    updated_at?: string;
}

export interface StudentResourceData {
    id: number;
    name: string;
    email: string;
    class_id?: ClassResourceData | null;
    section_id?: SectionResourceData | null;
    created_at: string;
}

const props = defineProps<{
    student: StudentResourceData | { data: StudentResourceData };
}>();

// Normalize student data in case of Resource wrapping
const student = computed<StudentResourceData>(() => {
    if ('data' in props.student && props.student.data) {
        return props.student.data;
    }
    return props.student as StudentResourceData;
});

// Set Inertia Layout Breadcrumbs
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
            {
                title: 'Students',
                href: studentsRoute.index(),
            },
            {
                title: 'Student Details',
                href: '#',
            },
        ],
    },
});

// Delete confirmation modal state
const isDeleteDialogOpen = ref(false);
const deleteForm = useForm({});

function handleConfirmDelete() {
    deleteForm.delete(studentsRoute.destroy(student.value.id).url, {
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
        },
    });
}

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
    <Head :title="student.name ? `${student.name} - Student Details` : 'Student Details'" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 max-w-4xl mx-auto w-full">
        <!-- Top Action & Navigation Header -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <Button as-child variant="outline" size="icon" class="h-9 w-9">
                    <Link :href="studentsRoute.index()">
                        <ArrowLeft class="h-4 w-4" />
                        <span class="sr-only">Back to Students</span>
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Student Profile</h1>
                    <p class="text-sm text-muted-foreground">
                        Detailed information for student record #{{ student.id }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <Button as-child variant="outline" class="gap-2">
                    <Link :href="studentsRoute.edit(student.id)">
                        <Pencil class="h-4 w-4" />
                        <span>Edit Student</span>
                    </Link>
                </Button>

                <Button variant="destructive" class="gap-2" @click="isDeleteDialogOpen = true">
                    <Trash2 class="h-4 w-4" />
                    <span>Delete</span>
                </Button>
            </div>
        </div>

        <!-- Student Profile Overview Header Card -->
        <Card class="border-sidebar-border/70 overflow-hidden shadow-xs">
            <div class="p-6 md:p-8 bg-linear-to-r from-primary/5 via-primary/10 to-transparent">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
                    <!-- Big Avatar Circle -->
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground font-bold text-2xl shadow-md border-2 border-background">
                        {{ getInitials(student.name) }}
                    </div>

                    <!-- Name & Quick Info -->
                    <div class="space-y-1.5 flex-1">
                        <div class="flex flex-wrap items-center gap-2.5">
                            <h2 class="text-2xl font-bold text-foreground">
                                {{ student.name }}
                            </h2>
                            <Badge variant="secondary" class="rounded-full px-2.5 py-0.5 text-xs font-semibold">
                                ID: #{{ student.id }}
                            </Badge>
                        </div>
                        <p class="text-sm text-muted-foreground flex items-center gap-1.5">
                            <Mail class="h-4 w-4 text-muted-foreground" />
                            {{ student.email }}
                        </p>
                    </div>

                    <!-- Status / Badge -->
                    <div class="flex flex-wrap sm:flex-col items-start sm:items-end gap-2 shrink-0">
                        <Badge variant="outline" class="gap-1.5 bg-background/80 backdrop-blur-xs py-1 px-3">
                            <ShieldCheck class="h-3.5 w-3.5 text-emerald-600 dark:text-emerald-400" />
                            <span>Active Student</span>
                        </Badge>
                    </div>
                </div>
            </div>
        </Card>

        <!-- Information Cards Grid -->
        <div class="grid gap-6 md:grid-cols-2">
            <!-- Academic Information -->
            <Card class="border-sidebar-border/70 shadow-xs">
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <School class="h-5 w-5 text-primary" />
                        <span>Academic Assignment</span>
                    </CardTitle>
                    <CardDescription>
                        Current class and section placement
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Class Details -->
                    <div class="flex items-center justify-between p-3.5 rounded-lg bg-muted/40 border border-sidebar-border/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-md bg-blue-100 dark:bg-blue-950/50 text-blue-700 dark:text-blue-400">
                                <School class="h-4 w-4" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground font-medium">Assigned Class</div>
                                <div class="font-semibold text-foreground">
                                    {{ student.class_id?.name || 'Unassigned' }}
                                </div>
                            </div>
                        </div>
                        <Badge
                            v-if="student.class_id?.name"
                            variant="outline"
                            class="bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800"
                        >
                            {{ student.class_id.name }}
                        </Badge>
                    </div>

                    <!-- Section Details -->
                    <div class="flex items-center justify-between p-3.5 rounded-lg bg-muted/40 border border-sidebar-border/50">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-md bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400">
                                <Layers class="h-4 w-4" />
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground font-medium">Assigned Section</div>
                                <div class="font-semibold text-foreground">
                                    {{ student.section_id?.name || 'Unassigned' }}
                                </div>
                            </div>
                        </div>
                        <Badge
                            v-if="student.section_id?.name"
                            variant="secondary"
                            class="bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800"
                        >
                            {{ student.section_id.name }}
                        </Badge>
                    </div>
                </CardContent>
            </Card>

            <!-- Personal Information & Metadata -->
            <Card class="border-sidebar-border/70 shadow-xs">
                <CardHeader>
                    <CardTitle class="text-base flex items-center gap-2">
                        <User class="h-5 w-5 text-primary" />
                        <span>Personal Details</span>
                    </CardTitle>
                    <CardDescription>
                        Account metadata and enrollment info
                    </CardDescription>
                </CardHeader>
                <CardContent class="space-y-4">
                    <!-- Full Name -->
                    <div class="flex items-center justify-between py-2 border-b border-sidebar-border/50">
                        <span class="text-sm text-muted-foreground">Full Name</span>
                        <span class="text-sm font-semibold text-foreground">{{ student.name }}</span>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center justify-between py-2 border-b border-sidebar-border/50">
                        <span class="text-sm text-muted-foreground">Email Address</span>
                        <span class="text-sm font-semibold text-foreground">{{ student.email }}</span>
                    </div>

                    <!-- Registration Date -->
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-muted-foreground flex items-center gap-1.5">
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                            <span>Registration Date</span>
                        </span>
                        <span class="text-sm font-semibold text-foreground">{{ student.created_at }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Delete Confirmation Modal Dialog -->
        <Dialog v-model:open="isDeleteDialogOpen">
            <DialogContent class="sm:max-w-md">
                <DialogHeader class="gap-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-destructive/10 text-destructive">
                        <AlertTriangle class="h-5 w-5" />
                    </div>
                    <div>
                        <DialogTitle class="text-lg">Delete Student Record?</DialogTitle>
                        <DialogDescription class="mt-1 text-sm">
                            Are you sure you want to delete <strong class="text-foreground">{{ student?.name }}</strong>? This action cannot be undone and will permanently remove student #{{ student?.id }} from the database.
                        </DialogDescription>
                    </div>
                </DialogHeader>
                <DialogFooter class="mt-4 gap-2 sm:gap-0">
                    <Button 
                        variant="outline" 
                        @click="isDeleteDialogOpen = false"
                        :disabled="deleteForm.processing"
                    >
                        Cancel
                    </Button>
                    <Button 
                        variant="destructive" 
                        @click="handleConfirmDelete"
                        :disabled="deleteForm.processing"
                        class="gap-2"
                    >
                        <Loader2 v-if="deleteForm.processing" class="h-4 w-4 animate-spin" />
                        <Trash2 v-else class="h-4 w-4" />
                        <span>{{ deleteForm.processing ? 'Deleting...' : 'Delete Student' }}</span>
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>
