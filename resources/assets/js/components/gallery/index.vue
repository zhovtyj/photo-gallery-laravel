<template>
    <div class="container mx-auto">
        <div class="shadow-md gallery-container">


            <!-----------TOP NAV--------->
            <nav class="flex items-center justify-between flex-wrap bg-blue-darkest p-6">
                <div class="block lg:hidden">
                    <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
                        <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
                    </button>
                </div>
                <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto justify-end">
                    <div class="justify-end">
                        <button @click="toggleCreateGallery" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-blue-darkest hover:bg-white mt-4 lg:mt-0">
                            <i class="fas fa-plus"></i> <span class="hover:text-blue-darkest">Add Gallery</span>
                        </button>
                    </div>
                </div>
            </nav>
            <!-----------END TOP NAV--------->

            <!-----------GALLERY--------->
            <div class="flex content-start flex-wrap">
                <div v-for="gallery in galleries" class="w-1/4 p-2">
                    <div class="max-w-xs rounded overflow-hidden shadow-lg">
                        <img class="w-full" :src="(gallery.photo && gallery.photo.url)?gallery.photo.url:'http://via.placeholder.com/350x250?text=No+Images'" :alt="gallery.title">
                        <div class="px-6 py-4">
                            <div class="font-bold text-xl mb-2">{{ gallery.title }}</div>
                            <p>
                                <span class="text-grey-darker text-sm font-bold mb-2">Number of photos:</span>
                                <span class="text-sm text-grey-dark">{{ gallery.photos.length }}</span>
                            </p>
                            <p class="text-grey-darker text-base">
                                {{ gallery.description }}
                            </p>
                        </div>
                        <hr />
                        <div class="px-6 py-1 flex flex-wrap justify-end">
                            <toggle-button @change="toggleGalleryStatus(gallery.id)" v-model="gallery.active"/>
                        </div>
                        <div class="px-6 py-4 flex flex-wrap justify-end">
                            <router-link :to="{name: 'gallery.show', params: {id:gallery.id}}" class="bg-green-light hover:bg-green text-grey-darkest mx-1 py-2 px-4 rounded">
                                <i class="fas fa-eye"></i>
                            </router-link>
                            <button @click="editGallery(gallery)" class="self-end bg-blue-light hover:bg-blue-dark text-white font-bold mx-1 py-2 px-4 rounded" type="button">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button @click="deleteGallery(gallery)" class="self-end bg-red-light hover:bg-red-dark text-white font-bold mx-1 py-2 px-4 rounded" type="button">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-----------END GALLERY--------->
        </div>

        <!-----------MODAL--------->
        <div v-if="modalGallery.show" @click.self="toggleCreateGallery" class="animated fadeIn fixed z-50 pin overflow-auto bg-smoke-dark flex" style="background: rgba(0,0,0,.4)">
            <div class="animated fadeInUp fixed shadow-inner max-w-md md:relative pin-b pin-x align-top m-auto justify-end md:justify-center p-8 bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">
                <h2 class="text-4xl text-center font-hairline md:leading-loose text-grey md:mt-8 mb-4">{{ modalGallery.title }}</h2>
                <form @submit.prevent="galleryFormHandler" class="bg-white" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="block text-grey-darker text-sm font-bold mb-2" for="title">
                            Title
                        </label>
                        <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="title" v-model="newGallery.title" placeholder="Title">
                    </div>

                    <div class="mb-4">
                        <label class="block text-grey-darker text-sm font-bold mb-2" for="description">
                            Description
                        </label>
                        <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="description" v-model="newGallery.description" placeholder="Description">
                            </textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <button @click="toggleCreateGallery" class="bg-grey-light hover:bg-grey-dark text-white font-bold py-2 px-4 rounded" type="button">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green hover:bg-green-dark text-white font-bold py-2 px-4 rounded">
                            {{ modalGallery.confirmButtonText }}
                        </button>
                    </div>
                </form>
                <span @click="toggleCreateGallery" class="absolute pin-t pin-r pt-4 px-4">
                        <svg class="h-12 w-12 text-grey hover:text-grey-darkest" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
            </div>
        </div>
        <!-----------END MODAL--------->



    </div>
</template>


<script>
    import swal from 'sweetalert2'
    import ToggleButton from 'vue-js-toggle-button/src/Button'

    export default {
        //mixins: [shortcutHelper],
        components: {
            ToggleButton,
        },
        data() {
            return {
                galleries: {},
                newGallery:{},
                modalGallery: {
                    show : false,
                    title : 'Create new Gallery',
                    confirmButtonText : 'Save',
                    action : 'store',
                },
            }
        },
        methods: {
            //TOGGLE MODAL FOR CREATE/EDIT GALLERY
            toggleCreateGallery() {
                this.modalGallery.show = !this.modalGallery.show;
                this.newGallery ={};
                this.modalGallery.title = 'Create new Gallery';
                this.modalGallery.confirmButtonText = 'Save new Gallery';
                this.modalGallery.action = 'store';
            },

            //GET ALL GALLERIES
            getGalleries() {
                axios.get('/api/gallery')
                    .then(response => {
                        this.galleries = response.data;
                        _.map(this.galleries, (gallery) => {
                            return _.assign(gallery.photo = {}, _.sample(gallery.photos));
                            //return _.assign(gallery.photo, _.head(gallery.photos));
                        });
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
            },

            //GALLERY MODAL FORM HANDLER
            galleryFormHandler()
            {
                if(this.modalGallery.action == 'store'){
                    this.storeGallery();
                }
                else if(this.modalGallery.action == 'update'){
                    this.updateGallery();
                }

            },

            //STORE NEW GALLERY
            storeGallery(){
                axios.post('/api/gallery', {
                    gallery: this.newGallery,
                })
                    .then(response => {
                        this.galleries.unshift(response.data);
                        this.toggleCreateGallery();
                    })
                    .catch(response => {
                        console.log(error);
                    });
            },

            //ACTIVATE/DEACTIVATE GALLERY
            toggleGalleryStatus(id){
                axios.put('/api/gallery/status/' + id, {
                    })
                    .then(response => {
                        //console.log(response);
                    })
                    .catch(response => {
                        console.log(response);
                    });
            },

            //DELETE GALLERY
            deleteGallery(gallery){
                swal({
                    title: "Are you sure?",
                    text: gallery.title+' gallery will be deleted',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: 'Delete',
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                }).then((result) => {
                    if (result.value) {
                        axios.delete('/api/gallery/' + gallery.id);
                        this.galleries = _.remove(this.galleries, function(gall) {
                            return gall.id != gallery.id;
                        });
                        swal({
                            title: "Success!",
                            type: "success",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            },

            //EDIT GALLERY
            editGallery(gallery){
                this.modalGallery.show = true;
                this.modalGallery.title = gallery.title+' Gallery Editing';
                this.modalGallery.confirmButtonText = 'Update';
                this.modalGallery.action = 'update';
                this.newGallery.id = gallery.id;
                this.newGallery.title = gallery.title;
                this.newGallery.description = gallery.description;
            },

            //UPDATE GALLERY
            updateGallery(){
                axios.put('/api/gallery/' + this.newGallery.id, {
                    gallery: this.newGallery,
                })
                    .then(response => {
                        var gallery = response.data;
                        _.assign(gallery.photo = {}, _.sample(gallery.photos));
                        this.galleries.splice(_.findIndex(this.galleries, {id: gallery.id}), 1, gallery);
                        this.toggleCreateGallery()
                    })
                    .catch(response => {
                        console.log(response);
                    });
            }
        },
        mounted() {
            this.getGalleries();
        }
    }

</script>

