<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { 
    GraduationCap, 
    Users, 
    School, 
    Layers, 
    ArrowRight, 
    CheckCircle2, 
    Sparkles, 
    ShieldCheck, 
    LayoutDashboard,
    LogIn,
    UserPlus
} from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard, login, register } from '@/routes';
</script>

<template>
    <Head title="Student Manager - Smart School Administration">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen flex flex-col bg-background text-foreground selection:bg-primary selection:text-primary-foreground">
        <!-- Top Navigation Bar -->
        <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur-md">
            <div class="max-w-7xl mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                <!-- App Brand & Logo -->
                <div class="flex items-center gap-2.5">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-md">
                        <GraduationCap class="h-6 w-6" />
                    </div>
                    <div>
                        <span class="font-bold text-lg tracking-tight text-foreground leading-none block">StudentManager</span>
                        <span class="text-xs text-muted-foreground font-medium">Academic Portal</span>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex items-center gap-3">
                    <template v-if="$page.props.auth?.user">
                        <Button as-child variant="default" class="gap-2 shadow-xs">
                            <Link :href="dashboard()">
                                <LayoutDashboard class="h-4 w-4" />
                                <span>Go to Dashboard</span>
                            </Link>
                        </Button>
                    </template>
                    <template v-else>
                        <Button as-child variant="ghost" class="gap-2">
                            <Link :href="login()">
                                <LogIn class="h-4 w-4" />
                                <span>Log in</span>
                            </Link>
                        </Button>

                        <Button as-child variant="default" class="gap-2 shadow-xs">
                            <Link :href="register()">
                                <UserPlus class="h-4 w-4" />
                                <span>Get Started</span>
                            </Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Main Landing Hero Section -->
        <main class="flex-1">
            <!-- Hero Banner -->
            <section class="relative overflow-hidden pt-12 pb-16 md:pt-20 md:pb-24 border-b border-border/40">
                <!-- Background Gradient Accent -->
                <div class="absolute inset-0 bg-linear-to-b from-primary/5 via-transparent to-transparent pointer-events-none"></div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                    <div class="mx-auto max-w-3xl text-center space-y-6">
                        <!-- Top Tagline Badge -->
                        <div class="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3.5 py-1 text-xs font-semibold text-primary">
                            <Sparkles class="h-3.5 w-3.5" />
                            <span>Smart Student & Academic Management System</span>
                        </div>

                        <!-- Main Heading -->
                        <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl text-foreground">
                            Effortless Student Records & Class Management
                        </h1>

                        <!-- Subtitle -->
                        <p class="text-lg text-muted-foreground leading-relaxed">
                            An all-in-one modern platform for educational institutions to organize student profiles, assign classes & sections, and monitor real-time academic stats.
                        </p>

                        <!-- CTA Actions -->
                        <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                            <template v-if="$page.props.auth?.user">
                                <Button as-child size="lg" variant="default" class="gap-2 shadow-md text-base px-6">
                                    <Link :href="dashboard()">
                                        <span>Open Dashboard</span>
                                        <ArrowRight class="h-5 w-5" />
                                    </Link>
                                </Button>
                            </template>
                            <template v-else>
                                <Button as-child size="lg" variant="default" class="gap-2 shadow-md text-base px-6">
                                    <Link :href="register()">
                                        <span>Start Free Account</span>
                                        <ArrowRight class="h-5 w-5" />
                                    </Link>
                                </Button>

                                <Button as-child size="lg" variant="outline" class="text-base px-6">
                                    <Link :href="login()">
                                        <span>Sign In</span>
                                    </Link>
                                </Button>
                            </template>
                        </div>

                        <!-- Highlights List -->
                        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 pt-6 text-sm text-muted-foreground">
                            <span class="flex items-center gap-1.5">
                                <CheckCircle2 class="h-4 w-4 text-emerald-500" /> Real-time Analytics
                            </span>
                            <span class="flex items-center gap-1.5">
                                <CheckCircle2 class="h-4 w-4 text-emerald-500" /> Dynamic Class-Section Filtering
                            </span>
                            <span class="flex items-center gap-1.5">
                                <CheckCircle2 class="h-4 w-4 text-emerald-500" /> Safe Modal Deletions
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Key Features Showcase -->
            <section class="py-16 md:py-24 bg-muted/30">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="text-center max-w-2xl mx-auto mb-16 space-y-2">
                        <Badge variant="outline" class="text-xs uppercase tracking-wider font-semibold">Features</Badge>
                        <h2 class="text-3xl font-bold text-foreground">Everything You Need for Administration</h2>
                        <p class="text-sm text-muted-foreground">Designed with Laravel 12, Inertia.js v3, and Vue 3 for lightning-fast performance.</p>
                    </div>

                    <!-- Feature Cards Grid -->
                    <div class="grid gap-8 md:grid-cols-3">
                        <!-- Card 1 -->
                        <Card class="border-sidebar-border/70 shadow-xs transition-transform hover:-translate-y-1">
                            <CardContent class="p-6 space-y-4">
                                <div class="h-12 w-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-semibold">
                                    <Users class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-foreground">Student Directory</h3>
                                <p class="text-sm text-muted-foreground">
                                    Comprehensive LIFO student listings, live instant search, page numbers pagination, and detailed profile views.
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Card 2 -->
                        <Card class="border-sidebar-border/70 shadow-xs transition-transform hover:-translate-y-1">
                            <CardContent class="p-6 space-y-4">
                                <div class="h-12 w-12 rounded-xl bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center font-semibold">
                                    <School class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-foreground">Class & Section Binding</h3>
                                <p class="text-sm text-muted-foreground">
                                    Organize students into classes and subsections with intelligent dependent dropdowns during student creation and editing.
                                </p>
                            </CardContent>
                        </Card>

                        <!-- Card 3 -->
                        <Card class="border-sidebar-border/70 shadow-xs transition-transform hover:-translate-y-1">
                            <CardContent class="p-6 space-y-4">
                                <div class="h-12 w-12 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-semibold">
                                    <Layers class="h-6 w-6" />
                                </div>
                                <h3 class="text-xl font-bold text-foreground">Interactive Dashboard</h3>
                                <p class="text-sm text-muted-foreground">
                                    Monitor total enrollment metrics, recent student registrations, and visual class distribution breakdowns in real-time.
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/40 py-8 bg-background">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-muted-foreground">
                <div class="flex items-center gap-2">
                    <GraduationCap class="h-4 w-4 text-primary" />
                    <span>Student Manager System &copy; {{ new Date().getFullYear() }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <span>Built with Laravel, Inertia & Vue</span>
                </div>
            </div>
        </footer>
    </div>
</template>
