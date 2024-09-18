<script setup>
import AnalyticSegments from './AnalyticSegments.vue';
import AnalyticFilters from './AnalyticFilters.vue';
import AnalyticMenu from './AnalyticMenu.vue';
import {__} from '@/Composables/Translate';

const patientsLinks = [
    { heading: '', links: [
        {name: __('Overview'), href: '/analytics'}
    ]},
    { heading: __('Patients By Taxa Categories'), links: [
        {name: __('Overview'), href: '/analytics/taxa'},
        {name: __('Common Name'), href: '/analytics/taxa/prevalent-patients'},
        {name: __('Taxonomic Class'), href: '/analytics/taxa/class'},
        {name: __('Taxonomy Tree Map'), href: '/analytics/taxa/taxonomy-tree'}
    ]},
    { heading: __('Demographics and Health Indicators'), links: [
        {name: __('Overview'), href: '/analytics/demographics'},
        {name: __('Parallel Coordinates Health Indicators'), href: '/analytics/demographics/health-indicators'},
        {name: __('Age'), href: '/analytics/demographics/age'},
        {name: __('Sex'), href: '/analytics/demographics/sex'},
        {name: __('Weight'), href: '/analytics/demographics/weight'},
        {name: __('Attitude'), href: '/analytics/demographics/attitude'},
        {name: __('Body Condition'), href: '/analytics/demographics/body-condition'},
        {name: __('Dehydration'), href: '/analytics/demographics/dehydration'},
        {name: __('Mucous Membrane'), href: '/analytics/demographics/mucous-membrane'},
    ]}
];

const locationLinks = [
    { heading: __('Patients by Location Found'), links: [
        {name: __('Overview'), href: '/analytics/origin'},
        {name: __('State Found'), href: '/analytics/origin/subdivision'},
        {name: __('City Found'), href: '/analytics/origin/city'},
        {name: __('Location Map'), href: '/analytics/origin/map'}
    ]},
    { heading: __('Patient Locations'), links: [
        {name: __('Overview'), href: '/analytics/location'},
        {name: __('Current Facility'), href: '/analytics/location/facility'},
        {name: __('Current Area'), href: '/analytics/location/area'}
    ]}
];

const classificationTagLinks = [
    { heading: 'Circumstances of Admission', links: [
        {name: __('Overview'), href: '/analytics/circumstances-of-admission'},
        {name: __('All Circumstances'), href: '/analytics/circumstances-of-admission/all'},
        {name: __('Over Time'), href: '/analytics/circumstances-of-admission/over-time'},
        {name: __('Survival Rate'), href: '/analytics/circumstances-of-admission/survival-rate'},
    ]},
    { heading: 'Clinical Classifications', links: [
        {name: __('Overview'), href: '/analytics/clinical-classifications'},
        {name: __('All Classifications'), href: '/analytics/clinical-classifications/all'},
        {name: __('Over Time'), href: '/analytics/clinical-classifications/over-time'},
        {name: __('Survival Rate'), href: '/analytics/clinical-classifications/survival-rate'},
    ]}
];

const dispositionLinks = [
    { heading: '', links: [
        {name: __('Overview'), href: '/analytics/disposition'},
        {name: __('Releases'), href: '/analytics/disposition/releases'},
        {name: __('Transfers'), href: '/analytics/disposition/transfers'},
        {name: __('Survival Rate'), href: '/analytics/disposition/survival-rate'},
        {name: __('Location Map'), href: '/analytics/disposition/map'},
    ]}
];

const hotlineLinks = [
    { heading: '', links: [
        {name: __('Overview'), href: '/analytics/hotline'},
        {name: __('Call Duration'), href: '/analytics/hotline/call-duration'},
        {name: __('Location Map'), href: '/analytics/hotline/map'},
    ]}
];
</script>

<template>
  <div class="lg:flex justify-between flex-row-reverse items-end mb-8">
    <AnalyticFilters
      :filters="$page.props.analytics.filters"
      class="mb-4 lg:mb-0"
    />
    <AnalyticSegments
      :segments="$page.props.analytics.filters.segments"
    />
  </div>
  <div class="my-8 border-b border-gray-300">
    <div class="grid lg:grid-flow-col auto-cols-max md:auto-cols-min gap-8 pb-4">
      <AnalyticMenu
        :button="__('Patients')"
        :list="patientsLinks"
      />
      <AnalyticMenu
        :button="__('Locations')"
        :list="locationLinks"
      />
      <AnalyticMenu
        :button="__('Classification Tags')"
        :list="classificationTagLinks"
      />
      <AnalyticMenu
        :button="__('Disposition')"
        :list="dispositionLinks"
      />
      <AnalyticMenu
        :button="__('Hotline')"
        :list="hotlineLinks"
      />
    </div>
  </div>
</template>
