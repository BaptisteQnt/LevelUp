<!-- resources/js/pages/billing/Plans.vue -->
<script setup>
import { usePage } from '@inertiajs/vue3'
const props = defineProps({ stripeKey: String, prices: Array })
</script>

<template>
  <div class="space-y-4">
    <div v-for="p in prices" :key="p.id" class="p-4 border rounded-xl">
      <div class="font-medium">{{ p.name }}</div>
      <div class="opacity-70">{{ p.amount }}</div>
      <!-- Navigation pleine page vers ta route GET -->
      <a :href="route('billing.checkout', { price: p.id })" class="mt-3 inline-block px-4 py-2 rounded-xl bg-black text-white">
        S’abonner
      </a>
    </div>
  </div>
</template>
