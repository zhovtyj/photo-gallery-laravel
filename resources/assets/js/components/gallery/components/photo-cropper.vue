<template>
    <div v-if="cropperVisible" class="full-photo-overlay">
        <div class="cropper-overlay-container fixed pin z-50 h-screen w-screen">
            <div class="cropper-service-area">
                <div class="flex content-center flex-wrap h-full relative" style="width:650px;">
                    <div class="cropper-service-area-left">
                        <button @click="rotate" class="btn-cropper self-end font-bold hover:bg-blue-dark" type="button">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button @click="rotateBack" class="btn-cropper self-end font-bold hover:bg-blue-dark" type="button">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button @click="discardChanges" class="btn-cropper self-end font-bold hover:bg-blue-dark" type="button">
                            <i class="fas fa-ban"></i>
                        </button>
                    </div>
                    <div class="cropper-service-area-center flex content-center flex-wrap justify-center">
                        <vue-cropper
                                ref="cropper"
                                :guides=true
                                :view-mode=2
                                drag-mode="crop"
                                :auto-crop-area=0.5
                                :min-container-width=650
                                :min-container-height=490
                                :background=false
                                :rotatable=true
                                :src=photo.url
                                :alt=photo.title
                                :img-style=cropper.imgStyle
                                :cropmove=cropImage>
                        </vue-cropper>
                    </div>
                    <div class="cropper-service-area-bottom flex justify-end">
                        <button @click="closePhotoCropper" class="btn-cropper self-end font-bold hover:bg-red-dark" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                        <button @click="submitCrop" class="btn-cropper self-end font-bold hover:bg-green-dark" type="button">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="full-photo-overlay-close-button" role="button" tabindex="3" @click.stop="closePhotoCropper" @keyup.enter="closePhotoCropper">
                <div class="icon"></div>
            </div>
        </div>
    </div>
</template>


<script>

    import VueCropper from 'vue-cropperjs';

    export default {
        name: 'photoCropper',
        props: ['photo', 'cropperVisible'],
        components: {
            VueCropper,
        },
        data() {
            return {
                cropper:{
                    imgStyle : {},
                    visible: false,
                    output: {},
                },
            }
        },
        methods: {
            rotate () {
                this.$refs.cropper.rotate(90);
            },
            rotateBack () {
                this.$refs.cropper.rotate(-90);
            },
            discardChanges(){
                this.$refs.cropper.reset();
            },
            submitCrop(){
                this.photo.base64 = this.$refs.cropper.getCroppedCanvas().toDataURL();
                axios.put(route('api.backend.gallery.photo.update'), {
                    photo: this.photo,
                })
                    .then(response => {
                        if(this.photo.id == response.data.id){
                            this.$parent.selectedPhoto = response.data;
                            this.$parent.photos.splice(_.findIndex(this.$parent.photos, {id: this.photo.id}), 1, response.data);
                            this.closePhotoCropper();
                        }
                    })
                    .catch(response => {
                        console.log(error);
                    });
            },
            closePhotoCropper(){
                this.$parent.cropperVisible = false;
            },
            cropImage(){
                //console.log('This is cropImage function v1');
                //this returns image
                //this.cropper.output = this.$refs.cropper.getCroppedCanvas().toDataURL();
            },
        },

        watch: {

            cropperVisible: function (cropperVisible) {
                if(cropperVisible){
                    if (! document.body.classList.contains('overflow-hidden')) {
                        document.body.classList.add('overflow-hidden');
                    }
                }
                else{
                    if (document.body.classList.contains('overflow-hidden')) {
                        document.body.classList.remove('overflow-hidden')
                    }
                }
            },
        },

        mounted() {

        }
    }

</script>

<style>
    .cropper-overlay-container{
        background: rgba(10,10,10,.86);
    }
    .cropper-service-area{
        position:relative;
        margin:0 auto;
        top:50%;
        margin-top:-245px;
        height:490px;
        width:650px;
    }
    .cropper-service-area-left{
        position:absolute;
        left:-55px;
        height: 100%;
        width:45px;
        background: rgba(51, 62, 69, 0.8);
    }
    .cropper-service-area-center{
        position:relative;
        /*height:100%;*/
        width:100%;
        background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQAQMAAAAlPW0iAAAAA3NCSVQICAjb4U/gAAAABlBMVEXMzMz////TjRV2AAAACXBIWXMAAArrAAAK6wGCiw1aAAAAHHRFWHRTb2Z0d2FyZQBBZG9iZSBGaXJld29ya3MgQ1M26LyyjAAAABFJREFUCJlj+M/AgBVhF/0PAH6/D/HkDxOGAAAAAElFTkSuQmCC');
    }
    .cropper-modal{
        background: none;
        opacity: unset;
    }
    .cropper-container{
        /*min-width:650px;*/
        /*min-height:490px;*/
        /*height:490px;*/
    }
    .cropper-service-area-bottom{
        position:absolute;
        bottom:-55px;
        height: 45px;
        width:100%;
        background: rgba(51, 62, 69, 0.8);
    }
    .btn-cropper{
        width:46px;
        height:46px;
        line-height: 46px;
        text-align: center;
        color: #b1bec6;
    }
    .btn-cropper:hover{
        color:#fff;
    }


    .full-photo-overlay-close-button{
        background: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        height: 2.5rem;
        position: absolute;
        right: 0;
        top: 0;
        transition: background-color .25s ease-out;
        width: 2.5rem;
    }
    .full-photo-overlay-close-button .icon{
        color: #fff;
        cursor: pointer;
        height: 1rem;
        left: .7rem;
        margin: 50% 50% 0 0;
        position: absolute;
        right: 0;
        top: -.5rem;
        transition: .25s ease;
        width: 1rem;
    }
    .full-photo-overlay-close-button .icon::before{
        transform: rotate(-45deg);
        background: #fff;
        content: "";
        height: 2px;
        left: 5%;
        position: absolute;
        top: 50%;
        transition: .25s ease;
        width: 100%;
    }
    .full-photo-overlay-close-button .icon::after{
        transform: rotate(45deg);
        background: #fff;
        content: "";
        height: 2px;
        left: 5%;
        position: absolute;
        top: 50%;
        transition: .25s ease;
        width: 100%;
    }
    .full-photo-overlay-arrow-buttons .arrow{
        border-left: 2px solid #fff;
        border-top: 2px solid #fff;
        cursor: pointer;
        height: 1.5rem;
        position: absolute;
        width: 1.5rem;
    }
    .arrow.arrow-previous{
        left: 2rem;
        top: 50%;
        transform: rotate(-45deg);
    }
    .arrow.arrow-next{
        right: 2rem;
        top: 50%;
        transform: rotate(135deg);
    }
</style>