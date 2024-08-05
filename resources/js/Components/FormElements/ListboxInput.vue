<template>
  <div
    ref="listboxInputRef"
    class="pointer-events-none"
  >
    <div class="relative rounded-md shadow-sm">
      <input
        id="account-number"
        v-model="searchText"
        type="text"
        name="account-number"
        class="pointer-events-auto focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-10 sm:text-sm border-gray-300 rounded-md"
        placeholder="Select a person"
        @click="openListbox"
        @focus="openListbox"
        @blur="onBlur"
        @keydown="onKeydown"
      >
      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <SelectorIcon
          class="h-5 w-5 text-gray-400"
          aria-hidden="true"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { inject, getCurrentInstance, watch, ref, onMounted } from "vue";
import { SelectorIcon } from "@heroicons/vue/24/solid";

export default {
    components: {
        SelectorIcon,
    },
    setup() {
        const symbols = Object.getOwnPropertySymbols(getCurrentInstance().parent.provides);
        const listboxSymbol = symbols.find((symbol) => symbol.toString() == "Symbol(ListboxContext)");
        let api = inject(listboxSymbol);

        let searchText = ref(api.value.value);
        let listboxInputRef = ref(null);

        // to prevent closing options on 2nd click in input
        onMounted(() => {
            api.buttonRef.value = listboxInputRef.value;
        });

        function openListbox() {
            api.openListbox();
        }

        function onBlur() {
            searchText.value = api.value.value.name;
        }

        function onKeydown(event) {
            if (event.key == " ") return;
            api.optionsRef.value.dispatchEvent(new event.constructor(event.type, event));
        }

        watch(searchText, (value) => {
            if (api.value.value.name == value) return;
            api.clearSearch();
            api.openListbox();
            api.search(value);
        });

        watch(api.value, (value) => {
            searchText.value = value.name;
        });

        return { openListbox, onBlur, onKeydown, searchText, listboxInputRef };
    },
};
</script>
