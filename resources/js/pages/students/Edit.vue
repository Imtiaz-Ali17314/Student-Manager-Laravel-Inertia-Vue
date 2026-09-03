<script setup lang="ts">
import { computed, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, Loader2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { dashboard } from '@/routes';
import studentsRoute from '@/routes/students';

export interface SectionItem {
    id: number;
    name: string;
    class_id: number;
}

export interface ClassItem {
    id: number;
    name: string;
    sections?: SectionItem[];
}

export interface StudentResourceData {
    id: number;
    name: string;
    email: string;
    class_id?: { id: number; name: string } | number | null;
    section_id?: { id: number; name: string } | number | null;
    created_at?: string;
}

const props = defineProps<{
    student: StudentResourceData | { data: StudentResourceData };
    classes: ClassItem[] | { data: ClassItem[] };
}>();

// Normalize student data
const student = computed<StudentResourceData>(() => {
    if ('data' in props.student && props.student.data) {
        return props.student.data;
    }
    return props.student as StudentResourceData;
});

// Normalize classes array
const classList = computed<ClassItem[]>(() => {
    if (!props.classes) return [];
    return Array.isArray(props.classes) ? props.classes : props.classes.data || [];
});

// Initial Class ID extraction
const initialClassId = computed(() => {
    const classVal = student.value.class_id;
    if (!classVal) return '';
    return typeof classVal === 'object' ? String(classVal.id) : String(classVal);
});

// Initial Section ID extraction
const initialSectionId = computed(() => {
    const sectionVal = student.value.section_id;
    if (!sectionVal) return '';
    return typeof sectionVal === 'object' ? String(sectionVal.id) : String(sectionVal);
});

// Form state using Inertia useForm
const form = useForm({
    name: student.value.name || '',
    email: student.value.email || '',
    class_id: initialClassId.value,
    section_id: initialSectionId.value,
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
                title: 'Edit Student',
                href: '#',
            },
        ],
    },
});

// Filter sections based on selected class
const availableSections = computed<SectionItem[]>(() => {
    if (!form.class_id) return [];
    const selectedClass = classList.value.find(
        (c) => String(c.id) === String(form.class_id),
    );
    return selectedClass?.sections || [];
});

// Reset section_id when class_id is changed to a different class
watch(
    () => form.class_id,
    (newClassId) => {
        if (newClassId !== initialClassId.value) {
            form.section_id = '';
        } else {
            form.section_id = initialSectionId.value;
        }
    },
);

function submit() {
    form.put(studentsRoute.update(student.value.id).url);
}
</script>

<template>
    <Head :title="student.name ? `Edit ${student.name}` : 'Edit Student'" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 md:p-6 max-w-4xl mx-auto w-full">
        <!-- Header & Back Button -->
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="outline" size="icon" class="h-9 w-9">
                    <Link :href="studentsRoute.index()">
                        <ArrowLeft class="h-4 w-4" />
                        <span class="sr-only">Back to Students</span>
                    </Link>
                </Button>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-foreground">Edit Student</h1>
                    <p class="text-sm text-muted-foreground">
                        Update information for student record #{{ student.id }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <Card class="border-sidebar-border/70 shadow-xs">
            <CardHeader>
                <CardTitle class="text-lg">Edit Student Information</CardTitle>
                <CardDescription>
                    All fields marked with an asterisk (<span class="text-destructive">*</span>) are required.
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Student Full Name -->
                    <div class="space-y-2">
                        <Label for="name" class="font-medium">
                            Full Name <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="e.g. Alex Johnson"
                            :class="{ 'border-destructive': form.errors.name }"
                            required
                        />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- Student Email -->
                    <div class="space-y-2">
                        <Label for="email" class="font-medium">
                            Email Address <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="e.g. alex.johnson@example.com"
                            :class="{ 'border-destructive': form.errors.email }"
                            required
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Class & Section Row -->
                    <div class="grid gap-6 sm:grid-cols-2">
                        <!-- Select Class -->
                        <div class="space-y-2">
                            <Label for="class_id" class="font-medium">
                                Class <span class="text-destructive">*</span>
                            </Label>
                            <Select v-model="form.class_id">
                                <SelectTrigger 
                                    id="class_id"
                                    :class="{ 'border-destructive': form.errors.class_id }"
                                >
                                    <SelectValue placeholder="Select a Class" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="cls in classList"
                                        :key="cls.id"
                                        :value="String(cls.id)"
                                    >
                                        {{ cls.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.class_id" />
                        </div>

                        <!-- Select Section -->
                        <div class="space-y-2">
                            <Label for="section_id" class="font-medium">
                                Section <span class="text-destructive">*</span>
                            </Label>
                            <Select 
                                v-model="form.section_id"
                                :disabled="!form.class_id || availableSections.length === 0"
                            >
                                <SelectTrigger 
                                    id="section_id"
                                    :class="{ 'border-destructive': form.errors.section_id }"
                                >
                                    <SelectValue 
                                        :placeholder="
                                            !form.class_id 
                                                ? 'First select a Class' 
                                                : availableSections.length === 0 
                                                    ? 'No sections available' 
                                                    : 'Select a Section'
                                        " 
                                    />
                                </SelectTrigger>
                                <SelectContent v-if="availableSections.length > 0">
                                    <SelectItem
                                        v-for="section in availableSections"
                                        :key="section.id"
                                        :value="String(section.id)"
                                    >
                                        {{ section.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <p v-if="!form.class_id" class="text-xs text-muted-foreground">
                                Select a class first to view available sections.
                            </p>
                            <InputError :message="form.errors.section_id" />
                        </div>
                    </div>

                    <!-- Form Action Buttons -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-sidebar-border/50">
                        <Button 
                            as-child 
                            variant="outline" 
                            type="button"
                            :disabled="form.processing"
                        >
                            <Link :href="studentsRoute.index()">
                                Cancel
                            </Link>
                        </Button>

                        <Button 
                            type="submit" 
                            variant="default"
                            :disabled="form.processing"
                            class="gap-2"
                        >
                            <Loader2 v-if="form.processing" class="h-4 w-4 animate-spin" />
                            <Save v-else class="h-4 w-4" />
                            <span>{{ form.processing ? 'Saving...' : 'Update Student' }}</span>
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
