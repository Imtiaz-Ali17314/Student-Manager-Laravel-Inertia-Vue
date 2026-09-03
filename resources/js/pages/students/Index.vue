<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Search,
    UserPlus,
    MoreHorizontal,
    Pencil,
    Trash2,
    Eye,
    X,
    GraduationCap,
    Loader2,
    AlertTriangle,
} from '@lucide/vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Card } from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import Pagination from '@/components/Pagination.vue';
import type { Paginator } from '@/components/Pagination.vue';
import { dashboard } from '@/routes';
import studentsRoute from '@/routes/students';

// Interfaces matching StudentResource.php output
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
    /** Laravel paginated ResourceCollection */
    students: Paginator<StudentResourceData>;
}>();

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
        ],
    },
});

// Current page records from the paginator
const studentList = computed<StudentResourceData[]>(() => props.students.data ?? []);

// Search filter state
const searchQuery = ref('');

// Delete confirmation modal state
const studentToDelete = ref<StudentResourceData | null>(null);
const isDeleteDialogOpen = ref(false);
const deleteForm = useForm({});

function promptDelete(student: StudentResourceData) {
    studentToDelete.value = student;
    isDeleteDialogOpen.value = true;
}

function handleConfirmDelete() {
    if (!studentToDelete.value) return;
    deleteForm.delete(studentsRoute.destroy(studentToDelete.value.id).url, {
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
            studentToDelete.value = null;
        },
    });
}

// Computed filtered list (filters within the current page)
const filteredStudents = computed(() => {
    const query = searchQuery.value.trim().toLowerCase();
    if (!query) return studentList.value;

    return studentList.value.filter((student) => {
        const nameMatch = student.name.toLowerCase().includes(query);
        const emailMatch = student.email.toLowerCase().includes(query);
        const classMatch =
            student.class_id?.name.toLowerCase().includes(query) ?? false;
        const sectionMatch =
            student.section_id?.name.toLowerCase().includes(query) ?? false;

        return nameMatch || emailMatch || classMatch || sectionMatch;
    });
});

// Overall total across all pages
const totalStudents = computed(() => props.students.meta?.total ?? studentList.value.length);

// Helper for Student Avatar Initials
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
    <Head title="Students Directory" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6">
        <!-- Top Directory Header & Actions -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">
                        Students Directory
                    </h1>
                    <Badge variant="secondary" class="rounded-full px-2.5 py-0.5 text-xs font-semibold">
                        {{ totalStudents }} Total
                    </Badge>
                </div>
                <p class="text-sm text-muted-foreground">
                    Browse, search, edit, and manage student accounts and assignments.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <Button as-child variant="default" class="gap-2 shadow-xs">
                    <Link :href="studentsRoute.create()">
                        <UserPlus class="h-4 w-4" />
                        <span>Add Student</span>
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative w-full max-w-sm">
                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                <Input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search by name, email, class, section..."
                    class="pl-9 pr-9"
                />
                <button
                    v-if="searchQuery"
                    @click="searchQuery = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    title="Clear search"
                >
                    <X class="h-4 w-4" />
                </button>
            </div>

            <div class="text-xs text-muted-foreground self-end sm:self-center">
                Showing {{ filteredStudents.length }} of {{ studentList.length }} on this page
            </div>
        </div>

        <!-- Students Data Table Card -->
        <Card class="border-sidebar-border/70 overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-muted/50 text-xs font-semibold uppercase text-muted-foreground border-b border-sidebar-border/70">
                        <tr>
                            <th scope="col" class="px-6 py-3.5">Student</th>
                            <th scope="col" class="px-6 py-3.5">Email</th>
                            <th scope="col" class="px-6 py-3.5">Class</th>
                            <th scope="col" class="px-6 py-3.5">Section</th>
                            <th scope="col" class="px-6 py-3.5">Joined Date</th>
                            <th scope="col" class="px-6 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sidebar-border/50">
                        <template v-if="filteredStudents.length > 0">
                            <tr
                                v-for="student in filteredStudents"
                                :key="student.id"
                                class="transition-colors hover:bg-muted/40"
                            >
                                <!-- Student Name & Avatar -->
                                <td class="px-6 py-4 font-medium text-foreground">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-xs border border-primary/20">
                                            {{ getInitials(student.name) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-foreground">{{ student.name }}</div>
                                            <div class="text-xs text-muted-foreground">ID: #{{ student.id }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Email -->
                                <td class="px-6 py-4 text-muted-foreground">
                                    {{ student.email }}
                                </td>

                                <!-- Class -->
                                <td class="px-6 py-4">
                                    <Badge
                                        v-if="student.class_id?.name"
                                        variant="outline"
                                        class="bg-blue-50/50 text-blue-700 border-blue-200 dark:bg-blue-950/30 dark:text-blue-400 dark:border-blue-800"
                                    >
                                        {{ student.class_id.name }}
                                    </Badge>
                                    <span v-else class="text-xs text-muted-foreground italic">Not Assigned</span>
                                </td>

                                <!-- Section -->
                                <td class="px-6 py-4">
                                    <Badge
                                        v-if="student.section_id?.name"
                                        variant="secondary"
                                        class="bg-emerald-50/50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-800"
                                    >
                                        {{ student.section_id.name }}
                                    </Badge>
                                    <span v-else class="text-xs text-muted-foreground italic">Not Assigned</span>
                                </td>

                                <!-- Created At Date -->
                                <td class="px-6 py-4 text-muted-foreground text-xs">
                                    {{ student.created_at }}
                                </td>

                                <!-- Actions Dropdown -->
                                <td class="px-6 py-4 text-right">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button variant="ghost" size="icon" class="h-8 w-8">
                                                <MoreHorizontal class="h-4 w-4" />
                                                <span class="sr-only">Open menu</span>
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end" class="w-40">
                                            <DropdownMenuLabel>Actions</DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="studentsRoute.show(student.id)"
                                                    class="flex items-center gap-2 cursor-pointer"
                                                >
                                                    <Eye class="h-4 w-4 text-muted-foreground" />
                                                    <span>View Details</span>
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem as-child>
                                                <Link
                                                    :href="studentsRoute.edit(student.id)"
                                                    class="flex items-center gap-2 cursor-pointer"
                                                >
                                                    <Pencil class="h-4 w-4 text-muted-foreground" />
                                                    <span>Edit Student</span>
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem 
                                                @select.prevent="promptDelete(student)"
                                                class="flex items-center gap-2 text-destructive cursor-pointer focus:bg-destructive/10"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                                <span>Delete</span>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State -->
                        <template v-else>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="mx-auto flex max-w-md flex-col items-center justify-center text-center">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-muted">
                                            <GraduationCap class="h-6 w-6 text-muted-foreground" />
                                        </div>
                                        <h3 class="mt-4 text-base font-semibold text-foreground">No students found</h3>
                                        <p class="mt-1 text-sm text-muted-foreground">
                                            {{
                                                searchQuery
                                                    ? `No results found matching "${searchQuery}". Try clearing your search.`
                                                    : 'Get started by creating your first student record.'
                                            }}
                                        </p>
                                        <div class="mt-4 flex gap-2">
                                            <Button
                                                v-if="searchQuery"
                                                variant="outline"
                                                size="sm"
                                                @click="searchQuery = ''"
                                            >
                                                Clear Search
                                            </Button>
                                            <Button
                                                v-else
                                                as-child
                                                variant="default"
                                                size="sm"
                                            >
                                                <Link :href="studentsRoute.create()">
                                                    <UserPlus class="mr-1.5 h-4 w-4" />
                                                    Add Student
                                                </Link>
                                            </Button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Pagination controls -->
            <div
                v-if="students.meta && students.meta.last_page > 1"
                class="border-t border-sidebar-border/50 px-4 py-3"
            >
                <Pagination :paginator="students" />
            </div>
        </Card>

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
                            Are you sure you want to delete <strong class="text-foreground">{{ studentToDelete?.name }}</strong>? This action cannot be undone and will permanently remove student #{{ studentToDelete?.id }} from the database.
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
