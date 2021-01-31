<template>
  <app-layout>

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Books  </h2>
    </template>

    <div class="py-12" v-if="$page.props.isAdmin">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

            <div class="p-6">
              <div class="flex items-center text-indigo-700 text-xl">
                All Books
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


  </app-layout>
</template>

<script>
import apiVersion from "@/apiVersion";
import axios from "axios";
import AppLayout from '@/Layouts/AppLayout';

export default {
  components: {
    AppLayout
  },
  props: {

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
