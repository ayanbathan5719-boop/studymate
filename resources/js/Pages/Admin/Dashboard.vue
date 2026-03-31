<template>
  <AuthenticatedLayout>
    <Head title="Admin Dashboard" />

    <div class="p-6">
      <h1 class="text-2xl font-bold text-blue-700">Admin Dashboard</h1>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Users</p>
              <p class="text-3xl font-bold text-gray-900">{{ stats.total_users }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
              <span class="text-2xl">👥</span>
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Students</p>
              <p class="text-3xl font-bold text-green-600">{{ stats.total_students }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
              <span class="text-2xl">🎓</span>
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Lecturers</p>
              <p class="text-3xl font-bold text-purple-600">{{ stats.total_lecturers }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
              <span class="text-2xl">👨‍🏫</span>
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Admins</p>
              <p class="text-3xl font-bold text-amber-600">{{ stats.total_admins }}</p>
            </div>
            <div class="bg-amber-100 rounded-full p-3">
              <span class="text-2xl">👑</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Second Row of Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Courses</p>
              <p class="text-3xl font-bold text-indigo-600">{{ stats.total_courses }}</p>
            </div>
            <div class="bg-indigo-100 rounded-full p-3">
              <span class="text-2xl">📚</span>
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Units</p>
              <p class="text-3xl font-bold text-teal-600">{{ stats.total_units }}</p>
            </div>
            <div class="bg-teal-100 rounded-full p-3">
              <span class="text-2xl">📖</span>
            </div>
          </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Forum Posts</p>
              <p class="text-3xl font-bold text-rose-600">{{ stats.total_forum_posts }}</p>
            </div>
            <div class="bg-rose-100 rounded-full p-3">
              <span class="text-2xl">💬</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Users and Activity -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Recent Users -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4">Recent Users</h2>
          <div class="space-y-4">
            <div v-for="user in stats.recent_users" :key="user.id"
                 class="flex items-center justify-between border-b pb-3 last:border-0">
              <div>
                <p class="font-medium">{{ user.name }}</p>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
              </div>
              <div class="flex items-center space-x-2">
                <span :class="getRoleBadgeClass(user.role)" class="px-2 py-1 rounded-full text-xs">
                  {{ user.role }}
                </span>
                <span class="text-xs text-gray-400">{{ formatDate(user.created_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white shadow rounded-lg p-6">
          <h2 class="text-lg font-semibold mb-4">Recent Activity</h2>
          <div class="space-y-4">
            <div v-for="log in stats.recent_logs" :key="log.id"
                 class="flex items-start space-x-3 border-b pb-3 last:border-0">
              <span class="text-xl">{{ getActionIcon(log.action) }}</span>
              <div class="flex-1">
                <p class="text-sm">
                  <span class="font-medium">{{ log.user }}</span>
                  {{ log.description }}
                </p>
                <p class="text-xs text-gray-400">{{ formatDate(log.time) }}</p>
              </div>
              <span :class="getActionColorClass(log.action)"
                    class="px-2 py-1 rounded-full text-xs">
                {{ log.action }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Props passed from the controller
defineProps({
  stats: Object
});

// Helper function to format dates properly
const formatDate = (timestamp) => {
  if (!timestamp) return 'N/A';
  
  const date = new Date(timestamp);
  
  // Check if date is valid
  if (isNaN(date.getTime())) return 'Invalid date';
  
  // Format: "Mar 13, 2026, 9:00 AM"
  return date.toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Helper functions for styling
const getRoleBadgeClass = (role) => {
  const classes = {
    'admin': 'bg-amber-100 text-amber-800',
    'lecturer': 'bg-purple-100 text-purple-800',
    'student': 'bg-green-100 text-green-800',
  };
  return classes[role?.toLowerCase()] || 'bg-gray-100 text-gray-800';
};

const getActionIcon = (action) => {
  const icons = {
    'LOGIN': '🔐',
    'LOGOUT': '🚪',
    'CREATE': '➕',
    'UPDATE': '✏️',
    'DELETE': '❌',
    'DENY_ACCESS': '⛔',
    'RESTORE_ACCESS': '✅',
  };
  return icons[action] || '📋';
};

const getActionColorClass = (action) => {
  const classes = {
    'LOGIN': 'bg-green-100 text-green-800',
    'LOGOUT': 'bg-gray-100 text-gray-800',
    'CREATE': 'bg-blue-100 text-blue-800',
    'UPDATE': 'bg-yellow-100 text-yellow-800',
    'DELETE': 'bg-red-100 text-red-800',
    'DENY_ACCESS': 'bg-red-100 text-red-800',
    'RESTORE_ACCESS': 'bg-green-100 text-green-800',
  };
  return classes[action] || 'bg-gray-100 text-gray-800';
};
</script>