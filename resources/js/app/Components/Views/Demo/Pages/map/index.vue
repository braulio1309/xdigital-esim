<template>
  <div class="content-wrapper">
    <app-breadcrumb :page-title="$t('map')"/>
      <div style="height: 800px;">
          <div class="card">
              <div class="card-header bg-transparent border-0">
                  <h5 class="m-0 p-0">{{ $t('current_location') }}</h5>
              </div>
              <div class="card-body px-3 pt-0 pb-3" style="height: 600px;">
                  <map-component :token="token"></map-component>
              </div>
          </div>
      </div>
  </div>
</template>
<script>
import MapComponent from './Map'
export default {
  components: { MapComponent },
  data() {
    return {
      token: 'AIzaSyC0egJSnbjKei31W5MWG-gmFVydakQuSY4'
    }
  }
}
</script>