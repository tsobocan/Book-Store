<template>
  <div class="relative flex items-top   min-h-screen bg-gray-100 dark:bg-gray-900   sm:pt-0">
    <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
      <inertia-link v-if="$page.props.user" href="/dashboard" class="text-sm text-gray-700 underline">
        Dashboard
      </inertia-link>

      <template v-else>
        <inertia-link :href="route('login')" class="text-sm text-gray-700 underline">
          Login
        </inertia-link>

        <inertia-link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
          Register
        </inertia-link>
      </template>
    </div>

    <div class="w-full mx-auto sm:px-6 lg:px-8 mt-6">
      <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">

        <div class="p-6">
          <div class="flex items-center text-indigo-700 text-xl">
            Book Store
          </div>

          <div class="flex items-center   mt-5">
            <table class="min-w-full">
              <thead>
              <tr>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">ID
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  Title
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  Author
                </th>
                <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                  Options
                </th>
              </tr>
              </thead>
              <tbody>
              <tr v-for="book in books">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                  <div class="flex items-center">{{ book.id }}.</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                  <div class="flex items-center">{{ book.title }}</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                  <div class="flex items-center">{{ book.author.name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                  <div class="flex items-center"></div>
                </td>
              </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>

<script>
import apiVersion from "@/apiVersion";
import axios from "axios";

export default {
  props: {
    canLogin: Boolean,
    canRegister: Boolean
  },
  data() {
    return {
      books: [],
    }
  },

  mounted() {
    let vm = this;
    axios.get(apiVersion.version + '/books/all').then((response) => {
      vm.books = response.data;
    });
  }
}
</script>
