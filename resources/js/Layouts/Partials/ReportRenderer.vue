<script setup>
import {inject, ref, computed, onMounted, onUnmounted, nextTick} from 'vue';
import printJS from 'print-js';
import PDFObject from 'pdfobject';
import { saveAs } from '@rdkmaster/file-saver';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/FormElements/SecondaryButton.vue';
import { PrinterIcon, ArrowDownTrayIcon } from '@heroicons/vue/24/outline';
import { useCookies } from "vue3-cookies";
import {__} from '@/Composables/Translate';
import axios from 'axios';
import { notify } from "notiwind"

const emitter = inject('emitter');

const { cookies } = useCookies();

//const url = ref('');
const url64 = ref('');
const showPdfModal = ref(false);

onMounted(() => {
    window.Echo.private(`device.${cookies.get('device-uuid')}`)
        .listen('ReportGenerating', (e) => {
            notify({
              title: __('You Report is in the Queue'),
              text: __('Please be patient while we generate your :reportTitle report.', {
                    reportTitle: e.reportTitle
              }),
            }, 7000);
        })
        .listen('ReportGenerated', (e) => {
            response(e.format, e.reportUrl);
        });
});

onUnmounted(() => window.Echo.leave(`device.${cookies.get('device-uuid')}`));

const fileName = (reportUrl) => {
    let url = reportUrl;
    // this removes the anchor at the end, if there is one
    url = url.substring(0, (url.indexOf('#') === -1) ? url.length : url.indexOf('#'));
    // this removes the query after the file name, if there is one
    url = url.substring(0, (url.indexOf('?') === -1) ? url.length : url.indexOf('?'));
    // this removes everything before the last slash in the path
    url = url.substring(url.lastIndexOf('/') + 1, url.length);

    return url;
};

const response = (reportFormat, reportUrl) => {
    //format.value = reportFormat;
    //url.value = reportUrl;

    if (reportFormat === 'pdf') {
        showPdfModal.value = true;

        url64.value = `/reports/stream?url=${window.btoa(reportUrl)}`;

        nextTick(() => {
            PDFObject.embed(url64.value, "#pdfRenderer", {
                pdfOpenParams: { view: "FitV", toolbar: 0 }
            });
        });

    } else if (reportFormat === 'export') {
        axios({
            url: reportUrl,
            method: 'GET',
            responseType: 'blob'
        }).then(response => {
            saveAs(new Blob([response.data]), fileName(reportUrl));
        });
    }

    emitter.emit('report-created');
};

const printPdf = () => printJS(url64.value);
</script>

<template>
  <DialogModal
    :show="showPdfModal"
    @close="showPdfModal = false"
  >
    <template #title>
      {{ __('Print or Save Report as PDF') }}
    </template>
    <template #content>
      <span class="relative z-0 inline-flex shadow-sm rounded-md">
        <button
          type="button"
          class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
          @click="printPdf"
        >
          <PrinterIcon class="h-6 w-6 mr-2" />
          {{ __('Print') }}
        </button>
        <a
          :href="`${url64}&disposition=download`"
          target="_blank"
          class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-blue-300 bg-blue-500 text-base font-medium text-white hover:bg-blue-60 focus:z-10 focus:outline-none focus:ring-1 focus:ring-blue-600 focus:border-blue-600"
        >
          <ArrowDownTrayIcon class="h-6 w-6 mr-2" />
          {{ __('Save as PDF') }}
        </a>
      </span>
      <div
        id="pdfRenderer"
        ref="embeded-pdf"
        class="mt-4 w-full"
        style="height: 500px;"
      />
    </template>
    <template #footer>
      <SecondaryButton @click="showPdfModal = false">
        {{ __('Close') }}
      </SecondaryButton>
    </template>
  </DialogModal>
</template>
