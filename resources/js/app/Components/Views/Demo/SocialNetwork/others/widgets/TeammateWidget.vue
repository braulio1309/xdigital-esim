<template>
    <div class="card rounded-lg" style="overflow: hidden;">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>{{ $t('friends') }}</div>
                <div class="">{{ teammates.length }}</div>
            </div>
        </div>
        <div class="card-body p-0">
            <app-pre-loader v-if="teammatesLoading"/>
            <template v-else>
                <div class="d-flex flex-column scroll-y custom-scrollbar" style="max-height: 380px;">
                    <a v-for="(mate, index) in teammates" :href="urlGenerator('social-profile/')"
                       class="d-flex align-items-center gap-x-1 py-2 px-3 text-dark teammate" :key="`teammates-item-${index}`">
                        <img width="30" height="30" class="rounded-circle"
                             :src="mate?.profile_picture?.full_url || urlGenerator('/images/avatar-demo.jpg')" alt="">
                        <span class="text-left">{{ mate.full_name }}</span>
                    </a>
                    <app-empty-data-block v-if="!teammates.length && !teammatesLoading" :message="$t('empty_data_block_team')"
                                          style="padding: 0 !important"/>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import {urlGenerator} from "../../../../../../Helpers/AxiosHelper";

export default {
    data() {
        return {
            urlGenerator,
            teammates: [],
            teammatesLoading: false
        }
    },
    mounted() {
        this.teammates = [
            {
                "id": 2,
                "first_name": "Mark",
                "last_name": "Nam",
                "uuid": "b1ba3062-ae36-413f-8e09-3846368d2082",
                "full_name": "Mark Nam",
                "profile_picture": null
            },
            {
                "id": 3,
                "first_name": "Mortimer",
                "last_name": "Waters",
                "uuid": "f1e66d22-6d65-4c15-acf3-6a8e60f661fb",
                "full_name": "Mortimer Waters",
                "profile_picture": null
            },
            {
                "id": 4,
                "first_name": "Alberto",
                "last_name": "Wiegand",
                "uuid": "0b21450a-e289-477f-900b-814b06dae16b",
                "full_name": "Alberto Wiegand",
                "profile_picture": null
            },
            {
                "id": 5,
                "first_name": "Emmet",
                "last_name": "Reinger",
                "uuid": "66359bd5-8921-4e48-8ba1-db44bcf5da04",
                "full_name": "Emmet Reinger",
                "profile_picture": null
            },
            {
                "id": 6,
                "first_name": "Cathrine",
                "last_name": "Doyle",
                "uuid": "b91cc16d-e9bf-4864-9bb6-ee5e52e62e58",
                "full_name": "Cathrine Doyle",
                "profile_picture": null
            },
            {
                "id": 7,
                "first_name": "Virginia",
                "last_name": "Runte",
                "uuid": "5d9168a0-202a-401e-acb6-4bba0c15f7dc",
                "full_name": "Virginia Runte",
                "profile_picture": null
            },
            {
                "id": 8,
                "first_name": "Lou",
                "last_name": "Runte",
                "uuid": "61ecc18f-bf8c-4643-b5e4-44d3d6d336e1",
                "full_name": "Lou Runte",
                "profile_picture": null
            },
            {
                "id": 9,
                "first_name": "Cleora",
                "last_name": "Shanahan",
                "uuid": "138fc4d6-2ac6-4a9d-8482-39cae7d2b6b4",
                "full_name": "Cleora Shanahan",
                "profile_picture": null
            },
            {
                "id": 10,
                "first_name": "Mina",
                "last_name": "Hamill",
                "uuid": "65f761af-468e-422c-bfb0-e1d33009b7b2",
                "full_name": "Mina Hamill",
                "profile_picture": null
            },
            {
                "id": 11,
                "first_name": "Marge",
                "last_name": "Cormier",
                "uuid": "5b4786b3-c761-435c-a4ba-61156fb24105",
                "full_name": "Marge Cormier",
                "profile_picture": null
            },
            {
                "id": 12,
                "first_name": "Baron",
                "last_name": "Sauer",
                "uuid": "1d0fda41-ca07-4781-b7c4-ef4ee2295c73",
                "full_name": "Baron Sauer",
                "profile_picture": null
            },
            {
                "id": 13,
                "first_name": "Maryse",
                "last_name": "Howe",
                "uuid": "f3fb92ff-0063-44cf-87c0-627153a2ffff",
                "full_name": "Maryse Howe",
                "profile_picture": null
            },
            {
                "id": 14,
                "first_name": "Dale",
                "last_name": "Braun",
                "uuid": "aabbac8c-babf-4b53-91b7-14beaccc1f26",
                "full_name": "Dale Braun",
                "profile_picture": null
            },
            {
                "id": 15,
                "first_name": "Else",
                "last_name": "Harvey",
                "uuid": "a8802ede-ee01-4681-a239-6cbe7b418ba0",
                "full_name": "Else Harvey",
                "profile_picture": null
            },
            {
                "id": 16,
                "first_name": "Kasey",
                "last_name": "Schroeder",
                "uuid": "3c93f6c5-e2ba-407f-8eaa-b23809175f60",
                "full_name": "Kasey Schroeder",
                "profile_picture": null
            },
            {
                "id": 17,
                "first_name": "Estevan",
                "last_name": "Herzog",
                "uuid": "8a59fd78-3491-485c-b913-27ebe70b5ddc",
                "full_name": "Estevan Herzog",
                "profile_picture": null
            },
            {
                "id": 18,
                "first_name": "Mustafa",
                "last_name": "Morar",
                "uuid": "ff743302-89d4-4c06-b3f4-1f2c80a099c7",
                "full_name": "Mustafa Morar",
                "profile_picture": null
            },
            {
                "id": 19,
                "first_name": "Shirley",
                "last_name": "Cruickshank",
                "uuid": "31baa41e-b2f1-40c2-8c31-260b40660635",
                "full_name": "Shirley Cruickshank",
                "profile_picture": null
            },
            {
                "id": 20,
                "first_name": "Verona",
                "last_name": "Streich",
                "uuid": "ea6414a4-0d1b-4834-98b7-d786b2eb0342",
                "full_name": "Verona Streich",
                "profile_picture": null
            },
            {
                "id": 21,
                "first_name": "Maryse",
                "last_name": "Dooley",
                "uuid": "d4816291-dc95-4657-ad15-b0621ff73215",
                "full_name": "Maryse Dooley",
                "profile_picture": null
            },
            {
                "id": 22,
                "first_name": "Freddy",
                "last_name": "Bins",
                "uuid": "5332919e-06f6-4092-8799-72d400f992fb",
                "full_name": "Freddy Bins",
                "profile_picture": null
            },
            {
                "id": 23,
                "first_name": "Jameson",
                "last_name": "Mayer",
                "uuid": "3b1be5ba-46ef-458d-a3d8-9a31813d99cb",
                "full_name": "Jameson Mayer",
                "profile_picture": null
            },
            {
                "id": 24,
                "first_name": "Grayce",
                "last_name": "DuBuque",
                "uuid": "f708b6d5-941c-40ca-bcd4-6b1a92de18f3",
                "full_name": "Grayce DuBuque",
                "profile_picture": null
            },
            {
                "id": 25,
                "first_name": "Ryleigh",
                "last_name": "Thompson",
                "uuid": "fe1fc696-1896-49da-85b8-9a4f030a1f27",
                "full_name": "Ryleigh Thompson",
                "profile_picture": null
            },
            {
                "id": 26,
                "first_name": "Sammie",
                "last_name": "Treutel",
                "uuid": "daa1f842-49a9-41da-9cbe-3011546d3c7c",
                "full_name": "Sammie Treutel",
                "profile_picture": null
            },
            {
                "id": 27,
                "first_name": "Hermann",
                "last_name": "Ortiz",
                "uuid": "aec52210-c0c4-4515-9f79-9409e0470914",
                "full_name": "Hermann Ortiz",
                "profile_picture": null
            },
            {
                "id": 28,
                "first_name": "Monserrate",
                "last_name": "Donnelly",
                "uuid": "46260333-f297-44a3-b0bf-598329ac7707",
                "full_name": "Monserrate Donnelly",
                "profile_picture": null
            },
            {
                "id": 29,
                "first_name": "Ardella",
                "last_name": "Goodwin",
                "uuid": "bd6c749c-ac02-4672-a7a3-062f6daaa557",
                "full_name": "Ardella Goodwin",
                "profile_picture": null
            },
            {
                "id": 30,
                "first_name": "Moriah",
                "last_name": "Runte",
                "uuid": "a82e7ce2-c817-4c47-8b68-f5d4aad2999d",
                "full_name": "Moriah Runte",
                "profile_picture": null
            },
            {
                "id": 31,
                "first_name": "Annabel",
                "last_name": "Crist",
                "uuid": "b4735250-90ae-433a-ad6e-62ab6e3624a0",
                "full_name": "Annabel Crist",
                "profile_picture": null
            },
            {
                "id": 32,
                "first_name": "Ryan",
                "last_name": "Rice",
                "uuid": "0bc11fbb-549a-4fab-b7b4-d8094816a405",
                "full_name": "Ryan Rice",
                "profile_picture": null
            }
        ]
    }
}
</script>

<style scoped lang="scss">
.teammate {
    &:hover {
        background-color: #dddddd50;
        color: #000 !important;
    }
}
</style>