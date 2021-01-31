<template>
  <app-layout>
    <notifications group="all" />

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Books </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

          <div class="p-6">
            <div class="flex items-center text-indigo-700 text-xl">
              All Books
            </div>

            <div class="flex items-center mt-5">
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
                    <div class="flex items-center">
                      <span role="button" @click="startAction('reserve', book)" v-if="$page.props.user">Reserve</span>
                    </div>
                  </td>
                </tr>
                </tbody>
              </table>

              <jet-dialog-modal :show="confirmAction" @close="closeModal">
                <template #title>
                  Select Dates
                </template>

                <template #content>
                  <div class="flex-col w-full mb-2" v-if="$page.props.isAdmin">
                    <label>User:</label>
                    <v-select v-model="form.selected" @search="fetchOptions" label="name" :options="options" class="w-full"/>
                  </div>
                  <div class="mb-1">Select dates from which you want to reserve book.</div>
                  <div class="inline-flex w-full">
                            <span class="w-full mr-2">
                              <label>{{ form.action === 'rent' ? 'Rent' : 'Reserve' }} from:</label>
                              <jet-input type="date" class="mt-1 block  w-full" placeholder="From date"
                                         v-model="form.fromDate"/>
                                   <jet-input-error :message="form.error.fromDate" class="mt-2"/>
                                </span>

                    <span class=" w-full ml-2">
                               <label>{{ form.action === 'rent' ? 'Rent' : 'Reserve' }} to:</label>
                            <jet-input type="date" class="mt-1 block  w-full" placeholder="To date"
                                       v-model="form.toDate"/>
                            <jet-input-error :message="form.error.toDate" class="mt-2"/>
                              </span>
                  </div>

                  <jet-input-error :message="form.error.other" class="mt-2"/>
                </template>

                <template #footer>
                  <jet-secondary-button @click.native="closeModal">
                    Close
                  </jet-secondary-button>

                  <jet-button class="ml-2" @click.native="confirmActionProcess"
                              :class="{ 'opacity-50': form.processing }" :disabled="form.processing">
                    {{ form.action === 'rent' ? 'Rent now' : 'Reserve now' }}
                  </jet-button>
                </template>
              </jet-dialog-modal>
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
import JetDialogModal from '@/Jetstream/DialogModal'
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetButton from '@/Jetstream/Button'

export default {
  components: {
    AppLayout, JetSecondaryButton, JetDialogModal, JetInput, JetInputError, JetButton
  },
  props: {},
  data() {
    return {
      confirmAction: false,
      books: [],
      options: [],
      form: {
        selected: null,
        book: null,
        fromDate: null,
        toDate: null,
        action: null,
        processing: false,
        error: {
          fromDate: null,
          toDate: null,
          other: null,
        }
      }
    }
  },

  mounted() {
    let vm = this;
    axios.get(apiVersion.version + '/books/all').then((response) => {
      vm.books = response.data;
    });
  },

  methods: {
    clearErrors() {
      this.form.error.fromDate = null;
      this.form.error.toDate = null;
      this.form.error.other = null;
    },

    closeModal() {
      this.confirmAction = false
    },

    fetchOptions (search, loading) {
      let vm = this;
      axios.post(route('users'), {query : search}).then((response) => {
        vm.options = response.data;
      });
    },

    startAction(action, book) {
      this.form.action = action;
      this.form.book = book;
      this.confirmAction = true;
    },

    confirmActionProcess() {

      this.form.processing = true;
      this.clearErrors();
      let vm = this;
      axios.post(route('action'), this.form).then(() => {
        vm.form.processing = false;
        vm.closeModal();
        vm.$notify({
          group: 'all',
          title: 'Saved',
        })
        vm.clearErrors();
      }).catch(error => {
        this.form.processing = false;
        if(error.response.data.errors.fromDate){
          this.form.error.fromDate = error.response.data.errors.fromDate[0];
        }
        if(error.response.data.errors.other){
          this.form.error.other = error.response.data.errors.other[0];
        }
        if(error.response.data.errors.toDate){
          this.form.error.toDate = error.response.data.errors.toDate[0];
        }
      });

    }

  }
}
</script>
