<template>
    <div id="map-container" class="map"></div>
</template>

<script>
import { Loader } from '@googlemaps/js-api-loader';

export default {
    props: ['token'],
    data() {
        return {
            loader: new Loader({
                apiKey: this.token,
                version: "weekly",
                libraries: ["places"]
            }),
            mapOptions: {
                center: { lat: 0, lng: 0 },
                zoom: 4,
            }
        }
    },
    methods: {
        getLocationPermission() {
            navigator.permissions.query({ name: 'geolocation' }).then((result) => {
                if (result.state === 'granted') {
                    console.log(granted)
                } else if (result.state === 'prompt') {
                    console.log('prompt')
                }
                // Don't do anything if the permission was denied.
            }).then(res => {
                console.log(res)
            })
        },
        setMarker(google, map, position) {
            new google.maps.Marker({
                position,
                map,
                title: 'test'
            })
        },
        getCurrentLocation(map, google) {
            let infoWindow = new google.maps.InfoWindow();

            // Try HTML5 geolocation.
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };

                        infoWindow.setPosition(pos);
                        // infoWindow.setContent("Location found.");
                        infoWindow.open(map);
                        map.setCenter(pos);
                        map.setZoom(11);
                        infoWindow.setContent("Your location");
                        // set marker
                        this.setMarker(google, map, pos)
                    },
                    () => {
                        handleLocationError(true, infoWindow, map.getCenter());
                    }
                );

                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                    infoWindow.setPosition(pos);
                    infoWindow.setContent(
                        browserHasGeolocation
                            ? "Error: The Geolocation service failed."
                            : "Error: Your browser doesn't support geolocation."
                    );
                    infoWindow.open(map);
                }
            } else {
                this.getLocationPermission()
                handleLocationError(false, infoWindow, map.getCenter());
            }
        },
        init() {
            this.loader
                .load()
                .then((google) => {
                    let map = new google.maps.Map(document.getElementById("map-container"), this.mapOptions);

                    // current location button
                    const locationButton = document.createElement("button");
                    locationButton.textContent = "Get your location";
                    locationButton.classList.add("map--button");

                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(locationButton);

                    locationButton.addEventListener("click", () => this.getCurrentLocation(map, google));
                })
                .catch(e => {
                    // do something
                });
        }
    },
    mounted() {
        this.init()
    }
}
</script>

<style lang="scss">
.map {
    min-height: 450px;
    height: 100%;

    &--button {
        border: none;
        background-color: rgba(0, 0, 0, 0.5);
        color: #fff;
        padding: 5px 15px;
        border-radius: 10px;
        margin-top: 20px;
        font-size: 16px;
        outline: none !important;

        &:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }
    }
}
</style>

