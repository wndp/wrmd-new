<script>
import VueComboBlocks from 'vue-combo-blocks';
import { XMarkIcon } from '@heroicons/vue/24/outline';

export default {
    components: {
        VueComboBlocks,
        XMarkIcon
    },
    props: {
        options: {
          type: Array,
          required: true
        },
        selected: {
          type: Array,
          default: () => ([])
        },
        category: {
          type: String,
          required: true
        },
        patientId: {
          type: Number,
          required: true
        },
        validating: {
          type: Boolean,
          required: false
        },
    },
    emits: ['changed'],
    data() {
        return {
            filteredList: this.options,
            selectedItems: this.selected
        };
    },
    mounted() {
      if (this.selected.length === 0) {
        window.axios
          .get(this.route('patients.classifications.index', [this.patientId, this.category]))
          .then(response => {
            this.selectedItems = response.data.map(p => {
              return {
                label: p.prediction,
                value: p.prediction,
                accurate: p.accuracy === 1,
                isSuspected: p.is_suspected
              }
            })
          });
      }
    },
    methods: {
        itemToString(item) {
          return item ? item.name : '';
        },
        stateReducer(state, actionAndChanges) {
          const { changes, type } = actionAndChanges;
          switch (type) {
            case VueComboBlocks.stateChangeTypes.InputKeyUpEnter:
            case VueComboBlocks.stateChangeTypes.ItemClick:
              return {
                ...changes,
                isOpen: true, // keep menu open after selection.
                hoveredIndex: state.hoveredIndex,
                inputValue: '', // don't add the item string as input value at selection.
              };
            case VueComboBlocks.stateChangeTypes.InputBlur:
              return {
                ...changes,
                inputValue: '', // don't add the item string as input value at selection.
              };
            default:
              return changes;
          }
        },
        updateList(text) {
          this.filteredList = this.options.filter((item) => item.label.toLowerCase().includes(text.toLowerCase()));
        },
        handleSelectedItemChange(selectedItem) {
          if (!selectedItem) return;
          const index = this.selectedItems.indexOf(selectedItem);
          if (index > 0) {
            this.selectedItems = [
              ...this.selectedItems.slice(0, index),
              ...this.selectedItems.slice(index + 1),
            ];
          } else if (index === 0) {
            this.selectedItems = [...this.selectedItems.slice(1)];
          } else {
            // Add item
            selectedItem.accurate = true;
            this.selectedItems.push(selectedItem);
          }
          this.save()
        },
        save() {
            let data = this.selectedItems.map(tag => {
                return {
                    text: tag.label,
                    isSuspected: tag.isSuspected || false,
                    isValidated: this.validating
                };
            });
            window.axios.post(
                `/internal-api/patients/${this.patientId}/classification/${this.category}/training`,
                data
            );
        }
    }
};
</script>

<template>
  <VueComboBlocks
    v-slot="{
      getInputProps,
      getInputEventListeners,
      hoveredIndex,
      isOpen,
      getMenuProps,
      getMenuEventListeners,
      getItemProps,
      getItemEventListeners,
      getComboboxProps,
      openMenu,
    }"
    :itemToString="itemToString"
    :items="filteredList"
    :stateReducer="stateReducer"
    @input-value-change="updateList"
    @select="handleSelectedItemChange"
  >
    <div
      v-bind="getComboboxProps()"
      class="flex items-center flex-wrap focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border border-gray-300 rounded-md py-1 px-1"
    >
      <div>
        <ul class="flex space-x-2 flex-wrap">
          <li
            v-for="item in selectedItems"
            :key="item.value"
            class="inline-flex items-center px-2 py-0.5 m-1 rounded text-xs font-medium"
            :class="[item.accurate ? 'bg-blue-100 text-blue-800' : 'bg-orange-200 text-orange-800']"
          >
            {{ item.label }}
            <XMarkIcon
              class="h-4 w-4 ml-2 cursor-pointer"
              @click="handleSelectedItemChange(item)"
            />
          </li>
        </ul>
      </div>
      <div class="relative w-48">
        <input
          v-bind="getInputProps()"
          :name="`${category}_${patientId}`"
          placeholder="Search"
          class="focus:ring-0 block w-full sm:text-sm border-none p-0 outline-none w-full m-1"
          v-on="getInputEventListeners()"
          @focus="openMenu"
        >
        <Transition
          leaveActiveClass="transition duration-100 ease-in"
          leaveFromClass="opacity-100"
          leaveToClass="opacity-0"
        >
          <ul
            v-show="isOpen"
            v-bind="getMenuProps()"
            class="absolute z-20 top-full w-72 py-1 mt-1 overflow-auto text-base bg-white rounded-md shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
            v-on="getMenuEventListeners()"
          >
            <li
              v-for="(item, index) in filteredList"
              :key="item.value"
              v-bind="getItemProps({ item, index })"
              :class="[
                hoveredIndex === index ? 'text-blue-900 bg-blue-100' : 'text-gray-900',
                selectedItems.includes(item) ? 'bold' : 'normal',
                'cursor-default select-none relative py-2 px-4',
              ]"
              v-on="getItemEventListeners({ item, index })"
            >
              <span>{{ item.label }}</span>
            </li>
          </ul>
        </Transition>
      </div>
    </div>
  </VueComboBlocks>
</template>
