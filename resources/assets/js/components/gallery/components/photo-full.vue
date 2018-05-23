<template>
    <div v-if="photoFullVisible" class="full-photo-overlay">

        <div class="full-photo-overlay-background" @click.stop="closePhotoFull"></div>

        <div class="full-photo-overlay-content" @click.stop="closePhotoFull">
            <div class="full-photo-overlay-embed">
                <div class="full-photo-overlay-container">
                    <img width="auto" height="auto" :src="photo.url">
                </div>
                <p class="full-photo-overlay-description" v-if="photo.title">{{ photo.title }}</p>
            </div>
        </div>

        <div class="full-photo-overlay-close-button" role="button" tabindex="3" @click.stop="closePhotoFull" @keyup.enter="closePhotoFull">
            <div class="icon"></div>
        </div>

        <div class="full-photo-overlay-arrow-buttons" v-if="arrowsVisible">
            <div class="arrow arrow-previous" role="button" tabindex="2" @click="moveToPreviousItem" @keyup.enter="moveToPreviousItem"></div>
            <div class="arrow arrow-next" role="button" tabindex="1" @click="moveToNextItem" @keyup.enter="moveToNextItem"></div>
        </div>

    </div>
</template>


<script>

    export default {
        name: 'photoFull',
        props: ['photo', 'photos', 'photoFullVisible'],
        data() {
            return {
                arrowsVisible: false,
                allowEventListeners: true,
            }
        },
        methods: {
            closePhotoFull(){
                this.$parent.photoFullVisible = false;
            },
            moveToPreviousItem(){
                var currentIndex = _.findIndex(this.photos, (photo) => { return photo.id == this.photo.id; });
                var prevPhoto = _.find(this.photos, (el, i, col) =>{
                    return i == currentIndex-1;
                });

                if(!prevPhoto){
                    prevPhoto = _.last(this.photos);
                }

                this.$parent.selectPhoto(prevPhoto);
            },
            moveToNextItem(){
                var currentIndex = _.findIndex(this.photos, (photo) => { return photo.id == this.photo.id; });
                var nextPhoto = _.find(this.photos, (el, i, col) =>{
                    return i == currentIndex+1;
                });

                if(!nextPhoto){
                    nextPhoto = _.head(this.photos);
                }

                this.$parent.selectPhoto(nextPhoto);

            },
            showArrows()
            {
                if(_.size(this.photos) > 1){
                    this.arrowsVisible = true;
                }else{
                    this.arrowsVisible = false;
                }
            },

            startEventListeners(){
                this.allowEventListeners = true;
            },

            stopEventListeners(){
                this.allowEventListeners = false;
            },

            runEventListeners(){
                window.addEventListener('keyup', (event) => {
                    //Switch between the photos
                    if(this.allowEventListeners || this.photoFullVisible){
                        if (event.which === 27) {
                            this.closePhotoFull();
                        }
                        if (event.which === 39) {
                            this.moveToNextItem();
                        }
                        if (event.which === 37) {
                            this.moveToPreviousItem();
                        }
                    }
                });
            },
        },

        watch: {

            photoFullVisible: function (photoFullVisible) {
                if(photoFullVisible){
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
            photos: function(){
                this.showArrows();
            }
        },

        mounted() {
            this.runEventListeners();

        }
    }

</script>

<style>
    .full-photo-overlay{
        display: block;
        height: 100vh;
        left: 0;
        position: fixed;
        top: 0;
        width: 100vw;
        z-index: 999;
    }
    .full-photo-overlay-background{
        background: rgba(0,0,0,.75);
        cursor: default;
        display: block;
        height: 100%;
        left: 0;
        position: absolute;
        top: 0;
        width: 100%;
    }
    .full-photo-overlay-content{
        cursor: default;
        display: block;
        height: 100%;
        position: relative;
        width: 100%;
    }
    .full-photo-overlay-embed{
        bottom: 0;
        cursor: default;
        display: block;
        height: 80%;
        left: 0;
        margin: auto;
        position: absolute;
        right: 0;
        top: -2.5rem;
        width: 75%;
    }
    .full-photo-overlay-container{
        cursor: default;
        height: 100%;
        margin: 0 0 1.5rem;
        position: relative;
        text-align: center;
        width: 100%;
    }
    .full-photo-overlay-container img{
        background-color: #000;
        bottom: 0;
        box-shadow: 0 0 1.5rem rgba(0,0,0,.45);
        cursor: default;
        display: block;
        left: 0;
        margin: auto;
        max-height: 100%;
        max-width: 100%;
        position: absolute;
        right: 0;
        top: 0;
    }
    .full-photo-overlay-description{
        display: block;
        padding-top: 1rem;
        text-align: center;
        color: #fff;
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