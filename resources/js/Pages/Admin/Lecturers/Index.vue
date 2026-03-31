<template>
  <AppLayout>
    <div class="p-6">
      <h1 class="text-2xl font-bold text-blue-700 mb-4">Lecturers</h1>

      <!-- Success message -->
      <div v-if="$page.props.flash.success" class="bg-green-100 text-green-700 p-2 rounded mb-4">
        {{ $page.props.flash.success }}
      </div>

      <!-- Form to add lecturer -->
      <form @submit.prevent="submit" class="mb-6 space-y-4">
        <input v-model="form.name" type="text" placeholder="Name" class="border p-2 w-full" />
        <input v-model="form.email" type="email" placeholder="Email" class="border p-2 w-full" />
        <input v-model="form.department" type="text" placeholder="Department" class="border p-2 w-full" />
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Lecturer</button>
      </form>

      <!-- List of lecturers -->
      <table class="w-full border">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 border">Name</th>
            <th class="p-2 border">Email</th>
            <th class="p-2 border">Department</th>
            <th class="p-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="lecturer in lecturers" :key="lecturer.id">
            <td class="p-2 border">{{ lecturer.name }}</td>
            <td class="p-2 border">{{ lecturer.email }}</td>
            <td class="p-2 border">{{ lecturer.department }}</td>
            <td class="p-2 border">
              <button @click="destroy(lecturer.id)" class="bg-red-600 text-white px-2 py-1 rounded">
                Delete
              </button>
            </td>
          </tr>
          <tr v-if="lecturers.length === 0">
            <td colspan="4" class="text-center text-gray-500 p-4">No lecturers found.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({ lecturers: Array });

const form = useForm({
  name: '',
  email: '',
  department: '',
});

function submit() {
  form.post(route('lecturers.store'));
}

function destroy(id) {
  form.delete(route('lecturers.destroy', id));
}
</script>
