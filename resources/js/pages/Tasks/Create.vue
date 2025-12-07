<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

import AppLayout from '@/layouts/AppLayout.vue';
import { create as taskCreate, store as taskStore } from '@/routes/tasks';
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    title: '',
    description: '',
    due: '',
});

const submit = () => {
    form.post(taskStore().url, {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Task" />

    <AppLayout
        :breadcrumbs="[{ title: 'Create Task', href: taskCreate().url }]"
    >
        <div class="m-4 flex h-full flex-1 flex-col gap-4 p-4">
            <div class="mx-auto w-full max-w-2xl">
                <!-- FLASH SUCCESS -->
                <Card
                    v-if="page.props.flash?.success"
                    class="mb-6 border-green-500 bg-green-50 dark:bg-green-900/10"
                >
                    <CardHeader>
                        <CardTitle class="text-green-700 dark:text-green-300">
                            Success
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="text-green-700 dark:text-green-300">
                        <p>{{ page.props.flash?.success.message }}</p>
                        <div
                            class="mt-4 rounded-md border border-green-200 bg-white p-4 dark:border-green-800 dark:bg-zinc-900"
                        >
                            <h4 class="font-semibold">Task Details</h4>
                            <ul class="mt-2 list-inside list-disc">
                                <li>
                                    <strong>Title:</strong>
                                    {{ page.props.flash?.success.task.title }}
                                </li>
                                <li v-if="page.props.flash?.success.task.description">
                                    <strong>Description:</strong>
                                    {{ page.props.flash?.success.task.description }}
                                </li>
                                <li>
                                    <strong>Status:</strong>
                                    {{ page.props.flash?.success.task.status.name }}
                                </li>
                                <li>
                                    <strong>Due:</strong>
                                    {{
                                        new Date(
                                            page.props.flash?.success.task.due,
                                        ).toLocaleString()
                                    }}
                                </li>
                                <li>
                                    <strong>Created by:</strong>
                                    {{ page.props.flash?.success.task.user.name }}
                                </li>
                            </ul>
                        </div>
                    </CardContent>
                </Card>

                <!-- TASK CREATE FORM -->
                <Card>
                    <CardHeader>
                        <CardTitle>Create New Task</CardTitle>
                        <CardDescription>
                            Enter the details of the new task below.
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="space-y-2">
                                <Label for="title">Title</Label>
                                <Input
                                    id="title"
                                    type="text"
                                    v-model="form.title"
                                    placeholder="Enter task title"
                                    required
                                />
                                <span
                                    v-if="form.errors.title"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.title }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="description">Description</Label>
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Enter task description"
                                />
                                <span
                                    v-if="form.errors.description"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.description }}
                                </span>
                            </div>

                            <div class="space-y-2">
                                <Label for="due">Due Date</Label>
                                <Input
                                    id="due"
                                    type="datetime-local"
                                    v-model="form.due"
                                    required
                                />
                                <span
                                    v-if="form.errors.due"
                                    class="text-sm text-red-500"
                                >
                                    {{ form.errors.due }}
                                </span>
                            </div>

                            <div class="flex justify-end">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                >
                                    {{
                                        form.processing
                                            ? 'Creating...'
                                            : 'Create Task'
                                    }}
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
