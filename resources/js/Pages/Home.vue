<template>
  <app-layout>
    <notifications group="all"/>

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Books </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

          <div class="p-6">
            <div class="flex justify-between w-full">
              <span class="text-indigo-700 text-xl font-black">All Books</span>
              <button type="button" @click="openBookManageModal(null)" v-if="$page.props.isAdmin"
                      class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                Add Book
              </button>
            </div>

            <div class="flex flex-col" v-if="$page.props.user && $page.props.isAdmin">
              <div class="font-bold">Search options</div>
              <div class="flex justify-between w-full bg-gray-100 p-2">
                <jet-input type="text" class="mt-1 block w-1/4" placeholder="Book Title" v-model="search.title"/>
                <jet-input type="text" class="mt-1 block w-1/4" placeholder="Author" v-model="search.author"/>
                <jet-input type="text" class="mt-1 block w-1/4" placeholder="Year" v-model="search.year"/>
                <button type="button" @click="getBooks"
                        class="inline-flex items-center px-4 py-2 bg-blue-200 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:text-gray-800 active:bg-gray-50 transition ease-in-out duration-150">
                  Search
                </button>
              </div>

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
                      <span role="button" @click="startAction('reserve', book)" class="mx-1" v-if="$page.props.user">Reserve</span>
                      <span role="button" @click="openBookManageModal(book)" class="mx-1"
                            v-if="$page.props.user && $page.props.isAdmin">Edit</span>
                      <span role="button" @click="deleteBook(book.id)" class="mx-1 text-red-500"
                            v-if="$page.props.user && $page.props.isAdmin">Delete</span>
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
                    <v-select v-model="form.selected" @search="fetchOptions" label="name" :options="options"
                              class="w-full"/>
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


              <jet-dialog-modal :show="confirmBookEdit" @close="closeBookModal">
                <template #title>
                  <span class="font-black"> Manage Book</span>
                </template>

                <template #content>
                  <div class="mb-1 text-gray-500">Here you can add/edit all details about books.</div>
                  <div class="inline-flex w-full mb-2">
                            <span class="w-full mr-2">
                              <label>Title:</label>
                              <jet-input type="text" class="mt-1 block  w-full" placeholder="Title"
                                         v-model="formBook.title"/>
                                   <jet-input-error :message="formBook.error.title" class="mt-2"/>
                                </span>

                    <span class=" w-full ml-2">
                             <label>Author:</label>
                            <jet-input type="text" class="mt-1 block  w-full" placeholder="Author"
                                       v-model="formBook.author"/>
                            <jet-input-error :message="formBook.error.author" class="mt-2"/>
                    </span>
                  </div>
                  <div class="inline-flex w-full">
                            <span class="w-full mr-2">
                              <label>Year:</label>
                              <jet-input type="number" class="mt-1 block  w-full" placeholder="Year of publication"
                                         v-model="formBook.year"/>
                                   <jet-input-error :message="formBook.error.year" class="mt-2"/>
                                </span>

                    <span class=" w-full ml-2">
                             <label>Quantity:</label>
                            <jet-input type="number" class="mt-1 block  w-full" placeholder="Quantity" min="1"
                                       v-model="formBook.quantity"/>
                            <jet-input-error :message="formBook.error.quantity" class="mt-2"/>
                    </span>
                  </div>

                </template>

                <template #footer>
                  <jet-secondary-button @click.native="closeBookModal">
                    Close
                  </jet-secondary-button>

                  <jet-button class="ml-2" @click.native="saveBookDetails"
                              :class="{ 'opacity-50': formBook.processing }" :disabled="formBook.processing">
                    Save
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
      confirmBookEdit: false,
      books: [],
      options: [],
      search : {
        title : null,
        year : null,
        author : null,
      },
      formBook: {
        processing: false,
        quantity: 0,
        id: null,
        author: null,
        title: null,
        year: null,
        error: {
          quantity: null,
          author: null,
          title: null,
          year: null,
        }
      },

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
    this.getBooks();

  },

  methods: {
    getBooks() {
      let vm = this;
      const query = Object.keys(this.search)
          .map(key => {
            if(this.search[key] !== null){
              return `${key}=${this.search[key]}`
            }
          }).filter(x => !!x)
          .join('&');
      axios.get(apiVersion.version + '/books?' + query).then((response) => {
        vm.books = response.data;
      });
    },
    deleteBook(id){
      let vm = this;
      axios.post(route('delete'), {id : id}).then((response) => {
        vm.$notify({
          group: 'all',
          title: 'Deleted.',
        });
        vm.getBooks();
      });
    },

    saveBookDetails() {

      this.formBook.processing = true;
      this.clearBookErrors();
      let vm = this;
      axios.post(route('edit'), this.formBook).then(() => {
        vm.formBook.processing = false;
        vm.closeBookModal();
        vm.$notify({
          group: 'all',
          title: 'Saved',
        })
        vm.clearBookErrors();
      }).catch(error => {
        this.formBook.processing = false;
        if (error.response.data.errors.title) {
          this.formBook.error.title = error.response.data.errors.title[0];
        }
        if (error.response.data.errors.year) {
          this.formBook.error.year = error.response.data.errors.year[0];
        }
        if (error.response.data.errors.author) {
          this.formBook.error.author = error.response.data.errors.author[0];
        }
        if (error.response.data.errors.quantity) {
          this.formBook.error.quantity = error.response.data.errors.quantity[0];
        }
      }).finally(() => vm.getBooks());
    },

    clearErrors() {
      this.form.error.fromDate = null;
      this.form.error.toDate = null;
      this.form.error.other = null;
    },

    clearBookErrors() {
      this.formBook.error.quantity = null;
      this.formBook.error.title = null;
      this.formBook.error.author = null;
      this.formBook.error.year = null;
    },

    closeModal() {
      this.confirmAction = false
    },

    closeBookModal() {
      this.confirmBookEdit = false
    },

    fetchOptions(search, loading) {
      let vm = this;
      axios.post(route('users'), {query: search}).then((response) => {
        vm.options = response.data;
      });
    },

    resetBookManage() {
      this.formBook.quantity = 0;
      this.formBook.id = null;
      this.formBook.author = null;
      this.formBook.title = null;
      this.formBook.year = null;
    },

    openBookManageModal(book = null) {
      this.resetBookManage();

      if (book !== null) {
        this.formBook.quantity = book.quantity;
        this.formBook.id = book.id;
        this.formBook.author = book.author.name;
        this.formBook.title = book.title;
        this.formBook.year = book.year;
      }
      this.confirmBookEdit = true;
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
        if (error.response.data.errors.fromDate) {
          this.form.error.fromDate = error.response.data.errors.fromDate[0];
        }
        if (error.response.data.errors.other) {
          this.form.error.other = error.response.data.errors.other[0];
        }
        if (error.response.data.errors.toDate) {
          this.form.error.toDate = error.response.data.errors.toDate[0];
        }
      });

    }

  }
}
</script>
