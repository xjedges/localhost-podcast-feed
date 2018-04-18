$(function(){
getUserIP(function(ip){
    var domain = "http://"+ip+"/Podcast"

    var Chrome = VueColor.Chrome;
    Vue.component('colorpicker', {
        components: {
            'chrome-picker': Chrome,
        },
        template: `
        <div class="input-group color-picker" ref="colorpicker">
            <input type="text" class="form-control" v-model="colorValue" @focus="showPicker()" @input="updateFromInput" />
            <span class="input-group-addon color-picker-container">
                <span class="current-color" :style="'background-color: ' + colorValue" @click="togglePicker()"></span>
                <chrome-picker :value="colors" @input="updateFromPicker" v-if="displayPicker" />
            </span>
        </div>`,
        props: ['color'],
        data() {
            return {
                colors: {
                    hex: '#000000',
                },
                colorValue: '',
                displayPicker: false,
            }
        },
        mounted() {
            this.setColor(this.color || '#000000');
        },
        methods: {
            setColor(color) {
                this.updateColors(color);
                this.colorValue = color;
            },
            updateColors(color) {
                if(color.slice(0, 1) == '#') {
                    this.colors = {
                        hex: color
                    };
                }
                else if(color.slice(0, 4) == 'rgba') {
                    var rgba = color.replace(/^rgba?\(|\s+|\)$/g,'').split(','),
                        hex = '#' + ((1 << 24) + (parseInt(rgba[0]) << 16) + (parseInt(rgba[1]) << 8) + parseInt(rgba[2])).toString(16).slice(1);
                    this.colors = {
                        hex: hex,
                        a: rgba[3],
                    }
                }
            },
            showPicker() {
                document.addEventListener('click', this.documentClick);
                this.displayPicker = true;
            },
            hidePicker() {
                document.removeEventListener('click', this.documentClick);
                this.displayPicker = false;
            },
            togglePicker() {
                this.displayPicker ? this.hidePicker() : this.showPicker();
            },
            updateFromInput() {
                this.updateColors(this.colorValue);
            },
            updateFromPicker(color) {
                this.colors = color;
                if(color.rgba.a == 1) {
                    this.colorValue = color.hex;
                }
                else {
                    this.colorValue = 'rgba(' + color.rgba.r + ', ' + color.rgba.g + ', ' + color.rgba.b + ', ' + color.rgba.a + ')';
                }
            },
            documentClick(e) {
                var el = this.$refs.colorpicker,
                    target = e.target;
                if(el !== target && !el.contains(target)) {
                    this.hidePicker()
                }
            }
        },
        watch: {
            colorValue(val) {
                if(val) {
                    this.updateColors(val);
                    this.$emit('input', val);
                }
            }
        },
    });
    new Vue({
        el: '#app',
        data(){
            return{
                list:list,
            }
        },
        methods:{
            getFeed(index){
                return $(".feed:nth-child("+(index+1)+")");
            },
            coverCapture(index,callback){
                var feed = this.getFeed(index);
                var self = this;
                self.list[index].isCapturing = true;
                html2canvas(feed.find(".coverHtml>div").get(0)).then(canvas => {
                    self.list[index].isCapturing = false;
                    self.list.isCaptured = true;

                    var coverCanvas = feed.find(".coverCanvas");
                    coverCanvas.empty();
                    coverCanvas.append(canvas);

                    if(callback)callback();
                });
            },
            upload(index){
                var feed = this.getFeed(index);
                var self = this;
                var canvas = feed.find(".coverCanvas>canvas");
                var coverData = canvas.get(0).toDataURL("image/png");
                // console.log(coverData.length, coverData);
                $.ajax({
                    method:"POST",
                    url: "gallery.php",
                    data: { 
                        domain:domain,
                        title: self.list[index].title,
                        author: self.list[index].author,
                        colorText: self.list[index].colorText,
                        colorBG: self.list[index].colorBG,
                        cover: coverData,
                    }
                }).done(function() {
                    self.list[index].url = domain+"/rss/"+self.list[index].title+".xml";
                });
            },
            update(index){
                if(this.list.isCaptured){
                    this.upload(index);
                }else{
                    var self = this;
                    self.coverCapture(index,function(){
                        self.upload(index);
                    });
                }
            }
        },
        mounted(){

        }
    });
})
})