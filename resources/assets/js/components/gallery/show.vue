<template>
    <div class="mx-auto">
        <div class="shadow-md gallery-container">

            <!-----------TOP NAV--------->
            <nav class="flex items-center justify-between flex-wrap bg-blue-darkest p-6">
                <div class="flex items-center flex-no-shrink text-white mr-6">
                    <span class="font-semibold text-xl tracking-tight">{{ gallery.title }}</span>
                </div>
                <div class="block lg:hidden">
                    <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
                        <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
                    </button>
                </div>
                <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto justify-end">
                    <div class="justify-end">
                        <button @click="trigerDropzone" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-blue-darkest hover:bg-white mt-4 lg:mt-0">
                            <i class="fas fa-plus"></i> Add Photos
                        </button>
                        <button @click="toggleUploadProgress"  :class="progressButtonVisible?'':'hidden'" class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-blue-darkest hover:bg-white mt-4 lg:mt-0">
                            <i class="fas fa-tasks"></i>
                            <span v-if="uploadProgressVisible">Hide Upload Progress</span>
                            <span v-else>Show Upload Progress</span>
                        </button>
                    </div>
                </div>
            </nav>
            <!-----------END TOP NAV--------->

            <div :class="(uploadProgressVisible)?'':'hidden'">
                <div id="custom-dropzone-preview" class="w-full w-full flex content-start flex-wrap relative vue-dropzone dropzone"></div>
            </div>

            <!-----------PHOTOS--------->

            <div class="flex content-start flex-wrap" style="min-height:400px;">
                <div class="w-3/4 flex content-start flex-wrap max-h-screen relative">
                    <div class="flex content-start flex-wrap overflow-auto w-full items-start pr-1">
                        <vue-dropzone
                                id="custom-dropzone-upload"
                                class="absolute pin"
                                v-on:vdropzone-sending="sendingEvent"
                                v-on:vdropzone-success-multiple="uploadDone"
                                v-on:vdropzone-drop="uploadStart"
                                ref="myVueDropzone"
                                :options="dropzoneOptions"
                                title="Click to upload files">
                        </vue-dropzone>

                        <draggable v-model="photos" @start="drag=true" @end="onDragEnd" :move="onMoveCallback" class="flex content-start flex-wrap w-full items-start">
                            <div v-for="photo in photos" :key="photo.id" class="w-full p-2 relative z-10 xl:w-1/5 lg:w-1/4 md:w-1/3 sm:w-1/2">
                                <div @click="selectPhoto(photo)" class="max-w-xs rounded overflow-hidden shadow border-grey-darker h-24 cursor-move bg-grey-lighter hover:shadow-lg hover:text-white hover:bg-blue gallery-item" :class="(selectedPhoto && photo.id == selectedPhoto.id)?'bg-blue':''">
                                    <div class="flex content-start flex-wrap">
                                        <div class="w-1/2 p-2 overflow-hidden h-24 cursor-pointer">
                                            <div @click="showPhotoFull(photo)" class="h-full bg-center bg-cover rounded shadow" :style="{ backgroundImage: 'url(' + photo.url + ')' }"></div>
                                        </div>
                                        <div class="w-1/2 p-2 pl-3 relative">
                                            <div class="absolute pin-t pin-r pr-1">
                                                <toggle-button @change="togglePhotoStatus(photo.id)" v-model="photo.active" :width="25" :height="13"/>
                                            </div>
                                            <p class="text-sm font-medium">{{ truncStr(photo.title, 14) }}</p>
                                            <p class="text-sm font-normal italic">{{ photo.width }} x {{ photo.height }}</p>
                                            <p class="text-sm font-medium">{{ parseInt(photo.size/1024) }} KB</p>
                                            <div class="flex absolute pin-b pin-r p-2">
                                                <div class="w-full self-end justify-end flex content-end relative">
                                                    <button @click="showCropper" class="self-end bg-blue-dark hover:bg-blue text-white font-bold py-1 px-2 mx-1 rounded" type="button">
                                                        <i class="fas fa-crop"></i>
                                                    </button>
                                                    <button @click="showPhotoFull(photo)" class="self-end bg-green-dark hover:bg-green text-white font-bold py-1 px-1 mx-1 rounded" type="button">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button @click="deletePhoto(photo)" class="self-end bg-red-dark hover:bg-red text-white font-bold py-1 px-2 mx-1 rounded" type="button">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div v-if="selectedPhoto" class="w-1/4 bg-white">
                    <img  @click="showPhotoFull(selectedPhoto)" class="w-full block cursor-pointer" :src="selectedPhoto.url" :alt="selectedPhoto.title">
                    <form @submit.prevent="photoFormHandler" class="bg-white">
                        <div class="p-3">
                            <div v-if="updatedMessageVisible" class="bg-green-lightest border border-green-light text-green-dark px-4 py-3 rounded relative" role="alert">
                                <strong class="font-bold"></strong>
                                <span class="block sm:inline">Changes saved successfully!</span>
                                <span class="absolute pin-t pin-b pin-r px-4 py-3">
                                <svg class="fill-current h-6 w-6 text-green" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                              </span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-grey-darker text-sm font-bold mb-2" for="title">
                                    Title:
                                </label>
                                <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="title" @focus="photoAttrFocused" @blur="photoAttrBlurred" v-model="editedPhoto.title" placeholder="Title">
                            </div>

                            <div class="mb-4">
                                <label class="block text-grey-darker text-sm font-bold mb-2" for="description">
                                    Description:
                                </label>
                                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-grey-darker" id="description" @focus="photoAttrFocused" @blur="photoAttrBlurred" v-model="editedPhoto.description" placeholder="Description">
                            </textarea>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">File Name:</span>
                                <a :href="selectedPhoto.url" target="_blank" class="no-underline"><span class="text-sm text-blue-darker">{{ selectedPhoto.file_name }}</span></a>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">Width:</span>
                                <span class="text-sm text-grey-dark">{{ selectedPhoto.width }} px</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">Height:</span>
                                <span class="text-sm text-grey-dark">{{ selectedPhoto.width }} px</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">Size:</span>
                                <span class="text-sm text-grey-dark">{{ parseInt(selectedPhoto.size/1024) }} KB</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">Uploaded:</span>
                                <span class="text-sm text-grey-dark">{{ selectedPhoto.created_at | moment("HH:mm, DD.MM.YYYY") }}</span>
                            </div>
                            <div class="mb-1">
                                <span class="text-grey-darker text-sm font-bold mb-2">Modified:</span>
                                <span class="text-sm text-grey-dark">{{ selectedPhoto.updated_at | moment("HH:mm, DD.MM.YYYY") }}</span>
                                <span></span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-----------END PHOTOS--------->

            <photo-full ref="photoFull" :photo="selectedPhoto" :photos="photos" :photoFullVisible="photoFullVisible"></photo-full>

            <photo-cropper ref="photoCropper" :photo="selectedPhoto" :cropperVisible="cropperVisible"></photo-cropper>


        </div>
    </div>
</template>


<script>

    import vue2Dropzone from 'vue2-dropzone'
    import 'vue2-dropzone/dist/vue2Dropzone.min.css'
    import photoFull from './components/photo-full.vue'
    import photoCropper from './components/photo-cropper.vue'
    import draggable from 'vuedraggable'
    import ToggleButton from 'vue-js-toggle-button/src/Button'

    export default {
        components: {
            vueDropzone: vue2Dropzone,
            photoFull,
            photoCropper,
            draggable,
            ToggleButton,
        },
        data() {
            return {
                dropzoneOptions: {
                    acceptedFiles: 'image/*',
                    clickable:true,
                    //headers: { Authorization: helper.getUserApiToken() },
                    maxFilesize: 5,
                    previewsContainer:'#custom-dropzone-preview',
                    url: '/api/gallery/photo/upload',
                    uploadMultiple: true,
                },
                gallery: {},
                photos: [],
                selectedPhoto: {},
                photoFullVisible:false,
                editedPhoto:{},
                updatedMessageVisible: false,
                cropperVisible: false,
                progressButtonVisible:false,
                uploadProgressVisible:false,
            }
        },
        computed: {
        },
        methods: {
            //FETCH GALLERY
            fetchGallery(){
                axios.get('/api/gallery/' + this.$route.params.id, {
                })
                    .then(response => {
                        this.gallery = response.data;
                        this.photos = this.gallery.photos;
                    })
                    .catch(response => {
                        console.log(response);
                    });
            },

            //DropZone Events
            //Start uploading photo
            sendingEvent (file, xhr, formData) {
                formData.append('gallery_id', this.gallery.id);
            },

            trigerDropzone(){
                //this.dropzoneOptions.clickable = true;
                this.$refs.myVueDropzone.$el.click();
            },

            uploadStart(event){
                this.progressButtonVisible =true;
            },
            toggleUploadProgress(){
                this.uploadProgressVisible = !this.uploadProgressVisible;
            },
            //Photos Uploaded
            uploadDone(files, response){
                _.forEach(response, (photo) => {
                    this.photos.push(photo);
                });
            },

            //Select photo
            selectPhoto(photo){
                if(_.findIndex(this.photos, (item) => { return photo.id == item.id; }) >= 0){
                    this.selectedPhoto = photo;
                    this.editedPhoto = photo;
                    this.$refs.photoFull.startEventListeners();
                }

            },

            //Photo Form Handler
            photoFormHandler(){

            },

            photoAttrFocused(){
                this.$refs.photoFull.stopEventListeners();
            },

            photoAttrBlurred(){
                this.updatePhotoAttr();
                this.$refs.photoFull.startEventListeners();
            },

            //Update photo attributes
            updatePhotoAttr(){
                axios.put('/api/gallery/photo/update-attr', {
                    photo: this.editedPhoto,
                })
                    .then(response => {
                        if(response.data.id == this.editedPhoto.id){
                            this.showUpdatedMessage();
                        }
                    })
                    .catch(response => {
                        console.log(error);
                    });
            },

            //ACTIVATE/DEACTIVATE PHOTO
            togglePhotoStatus(id){
                axios.put('/api/gallery/photo/status/' + id, {
                })
                    .then(response => {
                        //console.log(response);
                    })
                    .catch(response => {
                        console.log(response);
                    });
            },

            //Show photo
            showPhotoFull(photo){
                this.photoFullVisible = true;
                this.selectedPhoto = photo;
            },

            showCropper(){
                this.cropperVisible = true;
            },

            //Delete photo
            deletePhoto(photo){
                axios.delete('/api/gallery/photo/' + photo.id);
                this.moveToPreviousItem();
                this.photos = _.remove(this.photos, function(item) {
                    return item.id != photo.id;
                });
            },

            moveToPreviousItem(){
                var currentIndex = _.findIndex(this.photos, (photo) => { return photo.id == this.selectedPhoto.id; });
                var prevPhoto = _.find(this.photos, (el, i, col) =>{
                    return i == currentIndex-1;
                });

                if(!prevPhoto){
                    prevPhoto = _.last(this.photos);
                }

                this.selectPhoto(prevPhoto);
            },

            moveToNextItem(){
                var currentIndex = _.findIndex(this.photos, (photo) => { return photo.id == this.selectedPhoto.id; });
                var nextPhoto = _.find(this.photos, (el, i, col) =>{
                    return i == currentIndex+1;
                });

                if(!nextPhoto){
                    nextPhoto = _.head(this.photos);
                }

                this.selectPhoto(nextPhoto);

            },

            //Draggable
            onMoveCallback(event, originalEvent){
                return true;
            },

            onDragEnd(event){
                axios.put('/api/gallery/photo/order', {
                    galleryId: this.gallery.id,
                    oldOrder: event.oldIndex,
                    newOrder: event.newIndex,
                })
                    .then(response => {
                        if(response.data.id == this.editedPhoto.id){
                            this.showUpdatedMessage();
                        }
                    })
                    .catch(response => {
                        console.log(error);
                    });
            },

            showUpdatedMessage(){
                this.updatedMessageVisible = true;
                setTimeout(() => {
                    this.updatedMessageVisible = false;
                }, 700);
            },

            truncStr(str, n){
                if(str){
                    return (str.length > n) ? str.substr(0, n-1) + '...' : str;
                }
            },
        },
        watch: {

        },
        mounted() {
            this.fetchGallery();
        }
    }

</script>

<style>
    #custom-dropzone-upload {
        right:14px;
        background-color: transparent;
        color: #777;
        transition: background-color .2s linear;
        border: none;
    }
    .cursor-move{
        cursor:move;
    }
</style>

