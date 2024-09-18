<script setup>
import {ref, computed, onMounted} from 'vue';
import {usePage} from '@inertiajs/vue3';
import * as L from "leaflet";
import 'leaflet.fullscreen';
import { LMarkerClusterGroup } from 'vue-leaflet-markercluster'
import { LMap, LControlScale, LControlLayers, LTileLayer, LMarker, LPopup } from '@vue-leaflet/vue-leaflet';
import Loading from '@/Components/Loading.vue';
import isEmpty from 'lodash/isEmpty';
import omitBy from 'lodash/omitBy';
import isNil from 'lodash/isNil';
import axios from 'axios';

import 'leaflet/dist/leaflet.css';
import 'leaflet.fullscreen/Control.FullScreen.css';

const props = defineProps({
    id: {
        type: String,
        required: true
    },
    height: {
        type: Number,
        required: false,
        default: 500
    },
    urlParams: {
        type: Object,
        default: () => ({})
    }
});

let map = ref({});
const zoom = 7;
const maxZoom = 20;

const tileProviders = [
    {
      name: 'OpenStreetMap',
      visible: true,
      attribution:
        '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
    },
    {
      name: 'OpenTopoMap',
      visible: false,
      url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
      attribution:
        'Map data: &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>, <a href="http://viewfinderpanoramas.org">SRTM</a> | Map style: &copy; <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
    },
];

const mapOptions = {
    zoomSnap: 0.5,
    preferCanvas: true,
    fullscreenControl: true,
    fullscreenControlOptions: {
        position: 'topleft'
    }
};

const clusterOptions = {
    removeOutsideVisibleBounds: true,
    chunkedLoading: true,
};

const markerImages = ['blue','green','red','yellow','violet','orange','gold','gray','black'];

const data = ref([]);
const loading = ref(true);

const center = computed(() => {
    let coordinates = usePage().props.auth.team.coordinates;
    if (coordinates === null) return [0, 0];
    return L.latLng(coordinates.coordinates[1], coordinates.coordinates[0]);
});

const hasMarkerNames = computed(() => ! isEmpty(
    omitBy(data.value.map(marker => marker.name), isNil)
));

onMounted(() => getData());

const getData = () => {
    axios.get('/analytics/maps/' + props.id, {
        params: props.urlParams
    })
        .then(response => {
            data.value = response.data.series;
            loading.value = false;
        });
};

const leafletReady = (map) => {
    map.value = map;
    setFit();
};

const setFit = () => {
    if (isEmpty(data.value)) return;

    let markers = data.value[0].data.map(marker => {
        return L.marker([
            marker.coordinates.lat,
            marker.coordinates.lng
        ]);
    });

    map.value.fitBounds(
        L.featureGroup(markers).getBounds(),
        {
            maxZoom: zoom
        }
    );
};

const latLng = (marker) => L.latLng(marker.coordinates.lat, marker.coordinates.lng);

const icon = (index) => {
    return L.icon({
        iconUrl: getIconUrl(markerImages[index]),
        shadowUrl: new URL('../../../images/marker-shadow.png', import.meta.url).href,
        iconSize: [25, 41],
        iconAnchor: [12, 41],
        popupAnchor: [1, -34],
        shadowSize: [41, 41]
    });
};

const getIconUrl = (color) => new URL(`../../../images/marker-icon-${color}.png`, import.meta.url).href;
</script>

<template>
  <div class="bg-white overflow-hidden shadow rounded-lg">
    <div
      v-if="hasMarkerNames"
      class="flex flex-wrap sm:p-2 space-x-4"
      style="min-height: 65px;"
    >
      <template v-if="data.length">
        <template
          v-for="(markers, index) in data"
          :key="index"
        >
          <div class="flex items-center">
            <img
              :src="getIconUrl(markerImages[index])"
              :alt="markers.name"
              class="mr-2"
            >
            {{ markers.name }}
          </div>
        </template>
      </template>
    </div>
    <div :style="{height: `${height}px`}">
      <Loading
        v-if="loading"
        :style="{height: `${height}px`}"
      />
      <template v-else>
        <LMap
          ref="map"
          :maxZoom="maxZoom"
          :zoom="zoom"
          :center="center"
          :options="mapOptions"
          :useGlobalLeaflet="true"
          @ready="leafletReady"
        >
          <LControlScale position="topright" />
          <LControlLayers position="bottomleft" />
          <LTileLayer
            v-for="tileProvider in tileProviders"
            :key="tileProvider.name"
            :name="tileProvider.name"
            :visible="tileProvider.visible"
            :url="tileProvider.url"
            :attribution="tileProvider.attribution"
            layerType="base"
          />
          <LMarkerClusterGroup
            v-for="(markers, dataIndex) in data"
            :key="dataIndex"
            :options="clusterOptions"
          >
            <LMarker
              v-for="(marker, markerIndex) in markers.data"
              :key="`${dataIndex}.${markerIndex}`"
              :latLng="latLng(marker)"
              :icon="icon(dataIndex)"
            >
              <LPopup>
                <p><b>{{ marker.title }}</b></p>
                <p>{{ marker.content }}<br>Lat: {{ marker.coordinates.lat }}, Lng {{ marker.coordinates.lng }}</p>
              </LPopup>
            </LMarker>
          </LMarkerClusterGroup>
        </LMap>
      </template>
    </div>
  </div>
</template>
