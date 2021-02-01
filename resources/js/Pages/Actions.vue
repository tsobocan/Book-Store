<template>
  <app-layout>
    <notifications group="all"/>

    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Rents and Reservations </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center text-indigo-700 text-xl">
              All
            </div>
            <div class="flex items-center mt-5">
              <table class="min-w-full">
                <thead>
                <tr>
                  <th class="px-6 py-3 border-b-2 border-gray-300 text-left leading-4 text-blue-500 tracking-wider">Book
                  </th>
                  <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                    Reserved or Rented By
                  </th>
                  <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                    From-To
                  </th>
                  <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                    Status
                  </th>
                  <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 text-blue-500 tracking-wider">
                    Options
                  </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="bookAction in books">
                  <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <div class="flex flex-col  ">
                      <strong>
                        {{ bookAction.book.title }}, {{ bookAction.book.author.name }}, {{ bookAction.book.year }}
                      </strong>
                      <small>Code: {{ bookAction.actual_book.title }}</small>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <div class="flex items-center">
                      {{bookAction.user.name}}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <div class="flex flex-col">
                      <small> {{bookAction.valid_from}}</small>
                      <small> {{bookAction.valid_to}}</small>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <div class="flex items-center">
                      {{bookAction.current_status.title}}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">
                    <div class="flex items-center">
                      <span @click="rent(bookAction.id)" v-if="$page.props.isAdmin && bookAction.current_status.title !== 'Rented'">
                         <jet-secondary-button>Rent</jet-secondary-button>
                      </span>
                      <span @click="returnOrCancelBook(bookAction.id, 'cancel')" class="mx-1" v-if="$page.props.isAdmin && bookAction.current_status.title !== 'Rented'">
                         <jet-secondary-button>Cancel</jet-secondary-button>
                      </span>
                      <span @click="returnOrCancelBook(bookAction.id, 'return')" v-if="$page.props.isAdmin && bookAction.current_status.title === 'Rented'">
                         <jet-secondary-button><span class="whitespace-nowrap">Book returned</span></jet-secondary-button>
                      </span>
                    </div>
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
import axios from "axios";
import AppLayout from '@/Layouts/AppLayout';
import JetSecondaryButton from '@/Jetstream/SecondaryButton'
import JetInput from '@/Jetstream/Input'
import JetInputError from '@/Jetstream/InputError'
import JetButton from '@/Jetstream/Button'

export default {
  components: {
    AppLayout, JetSecondaryButton, JetInput, JetInputError, JetButton
  },
  props: {},
  data() {
    return {
      confirmAction: false,
      books: [],
      options: [],
    }
  },

  mounted() {
    this.getReservedAndRented();
  },

  methods: {
    getReservedAndRented() {
      let vm = this;
      axios.get(route('active')).then((response) => {
        vm.books = response.data;
      });
    },

    rent(id) {
      let vm = this;
      axios.post(route('rent'), {id: id}).then(() => {
        vm.$notify({
          group: 'all',
          title: 'Rented',
        });
        vm.getReservedAndRented();
      });
    },

    returnOrCancelBook(id, option) {
      let vm = this;
      let text = option === 'cancel' ? 'Canceled.' : 'Returned';
      axios.post(route('return'), {id: id}).then(() => {
        vm.$notify({
          group: 'all',
          title: text,
        })
        vm.getReservedAndRented();
      });
    }
  }
}
</script>
