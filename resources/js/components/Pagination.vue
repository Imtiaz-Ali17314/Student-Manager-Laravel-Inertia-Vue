<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
} from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';

/**
 * Individual page-number link inside meta.links[].
 */
export interface PaginatorLink {
    url: string | null;
    label: string;
    active: boolean;
}

/**
 * Top-level links object from Laravel ResourceCollection paginator.
 * Shape: { first, last, prev, next }
 */
export interface PaginatorLinks {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
}

/**
 * Laravel LengthAwarePaginator meta.
 * meta.links[] is the array of page-number link objects.
 */
export interface PaginatorMeta {
    current_page: number;
    last_page: number;
    from: number | null;
    to: number | null;
    total: number;
    per_page: number;
    path: string;
    links: PaginatorLink[];
}

/**
 * Full paginator shape sent by Inertia when using
 * ResourceCollection::collection(Model::paginate()).
 */
export interface Paginator<T = unknown> {
    data: T[];
    links: PaginatorLinks;
    meta: PaginatorMeta;
}

const props = defineProps<{
    paginator: Paginator;
}>();

const meta = computed<PaginatorMeta>(() => props.paginator.meta);

/**
 * Page-number links live inside meta.links[].
 * Filter out the «Previous» and «Next» sentinel entries.
 */
const pageLinks = computed<PaginatorLink[]>(() =>
    (meta.value.links ?? []).filter(
        (link) =>
            !link.label.includes('Previous') && !link.label.includes('Next'),
    ),
);

const prevUrl = computed<string | null>(() => props.paginator.links.prev ?? null);
const nextUrl = computed<string | null>(() => props.paginator.links.next ?? null);
const firstPageUrl = computed<string | null>(() => props.paginator.links.first ?? null);
const lastPageUrl = computed<string | null>(() => props.paginator.links.last ?? null);

const isFirstPage = computed(() => meta.value.current_page === 1);
const isLastPage = computed(() => meta.value.current_page === meta.value.last_page);
</script>

<template>
    <div class="flex flex-col items-center justify-between gap-4 px-2 sm:flex-row">
        <!-- Results summary -->
        <p class="text-sm text-muted-foreground">
            Showing
            <span class="font-medium text-foreground">{{ meta.from ?? 0 }}</span>
            –
            <span class="font-medium text-foreground">{{ meta.to ?? 0 }}</span>
            of
            <span class="font-medium text-foreground">{{ meta.total }}</span>
            results
        </p>

        <!-- Navigation controls -->
        <div class="flex items-center gap-1">
            <!-- First page -->
            <Button
                v-if="!isFirstPage && firstPageUrl"
                as-child
                variant="outline"
                size="icon"
                class="h-8 w-8"
            >
                <Link :href="firstPageUrl" preserve-scroll>
                    <ChevronsLeft class="h-4 w-4" />
                    <span class="sr-only">First page</span>
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon" class="h-8 w-8" disabled>
                <ChevronsLeft class="h-4 w-4" />
            </Button>

            <!-- Previous page -->
            <Button
                v-if="!isFirstPage && prevUrl"
                as-child
                variant="outline"
                size="icon"
                class="h-8 w-8"
            >
                <Link :href="prevUrl" preserve-scroll>
                    <ChevronLeft class="h-4 w-4" />
                    <span class="sr-only">Previous page</span>
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon" class="h-8 w-8" disabled>
                <ChevronLeft class="h-4 w-4" />
            </Button>

            <!-- Page number buttons -->
            <template v-for="link in pageLinks" :key="link.label">
                <Button
                    v-if="link.url && !link.active"
                    as-child
                    variant="outline"
                    size="icon"
                    class="h-8 w-8"
                >
                    <Link :href="link.url" preserve-scroll>
                        {{ link.label }}
                    </Link>
                </Button>
                <Button
                    v-else
                    :variant="link.active ? 'default' : 'outline'"
                    size="icon"
                    class="h-8 w-8"
                    :disabled="!link.url"
                    :aria-current="link.active ? 'page' : undefined"
                >
                    {{ link.label }}
                </Button>
            </template>

            <!-- Next page -->
            <Button
                v-if="!isLastPage && nextUrl"
                as-child
                variant="outline"
                size="icon"
                class="h-8 w-8"
            >
                <Link :href="nextUrl" preserve-scroll>
                    <ChevronRight class="h-4 w-4" />
                    <span class="sr-only">Next page</span>
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon" class="h-8 w-8" disabled>
                <ChevronRight class="h-4 w-4" />
            </Button>

            <!-- Last page -->
            <Button
                v-if="!isLastPage && lastPageUrl"
                as-child
                variant="outline"
                size="icon"
                class="h-8 w-8"
            >
                <Link :href="lastPageUrl" preserve-scroll>
                    <ChevronsRight class="h-4 w-4" />
                    <span class="sr-only">Last page</span>
                </Link>
            </Button>
            <Button v-else variant="outline" size="icon" class="h-8 w-8" disabled>
                <ChevronsRight class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
