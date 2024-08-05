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
          :max-zoom="maxZoom"
          :zoom="zoom"
          :center="center"
          :options="mapOptions"
          :use-global-leaflet="true"
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
            layer-type="base"
          />
          <LMarkercluster
            v-for="(markers, dataIndex) in data"
            :key="dataIndex"
            :options="clusterOptions"
          >
            <LMarker
              v-for="(marker, markerIndex) in markers.data"
              :key="`${dataIndex}.${markerIndex}`"
              :lat-lng="latLng(marker)"
              :icon="icon(dataIndex)"
            >
              <LPopup>
                <p><b>{{ marker.title }}</b></p>
                <p>{{ marker.content }}<br>Lat: {{ marker.coordinates.lat }}, Lng {{ marker.coordinates.lng }}</p>
              </LPopup>
            </LMarker>
          </LMarkercluster>
        </LMap>
      </template>
    </div>
  </div>
</template>

<script setup>
import * as L from "leaflet";
import FullScreen from 'leaflet.fullscreen/Control.FullScreen';
import LMarkercluster from '@/Components/Analytics/LeafletMarkercluster.vue';
import { LMap, LControlScale, LControlLayers, LTileLayer, LMarker, LIcon, LPopup } from '@vue-leaflet/vue-leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.fullscreen/Control.FullScreen.css';
import Loading from '@/Components/Loading.vue';
import isEmpty from 'lodash/isEmpty';
import isNil from 'lodash/isNil';

</script>

<script>
export default {
    props: {
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
        },
    },
    data () {
        return {
            map: {},
            loading: true,
            zoom: 7,
            maxZoom: 20,
            tileProviders: [
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
              ],
            mapOptions: {
                zoomSnap: 0.5,
                preferCanvas: true,
                fullscreenControl: true,
                fullscreenControlOptions: {
                    position: 'topleft'
                }
            },
            clusterOptions: {
                removeOutsideVisibleBounds: true,
                chunkedLoading: true,
            },
            data: [],
            markerImages: ['blue','green','red','yellow','violet','orange','gold','gray','black'],
        };
    },
    computed: {
        center() {
            let account = this.$page.props.auth.account;
            return L.latLng(account.coordinates.coordinates[1], account.coordinates.coordinates[0]);
        },
        hasMarkerNames() {
            return ! isEmpty(
                window._.chain(this.data).map(marker => marker.name).omitBy(isNil).value()
            );
        }
    },
    created() {
        this.getData();
    },
    methods: {
        getData() {
            window.axios.get('/analytics/maps/' + this.id, {
                params: this.urlParams
            })
                .then(response => {
                    this.data = response.data.series;
                    this.loading = false;
                });
        },
        leafletReady(map) {
            this.map = map;
            this.setFit();
        },
        setFit() {
            let markers = this.data[0].data.map(marker => {
                return L.marker([
                    marker.coordinates.lat,
                    marker.coordinates.lng
                ]);
            });

            this.map.fitBounds(
                L.featureGroup(markers).getBounds(),
                {
                    maxZoom: this.zoom
                }
            );
        },
        latLng(marker) {
            return L.latLng(marker.coordinates.lat, marker.coordinates.lng);
        },
        icon(index) {
            return L.icon({
                iconUrl: this.getIconUrl(this.markerImages[index]),
                shadowUrl: new URL('../../../images/marker-shadow.png', import.meta.url).href,
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        },
        getIconUrl(color) {
            return new URL(`../../../images/marker-icon-${color}.png`, import.meta.url).href;
        }
    },
};
</script>
