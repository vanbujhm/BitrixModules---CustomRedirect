import * as Vue from 'https://unpkg.com/vue@3.0.11/dist/vue.esm-browser.js';

const app = Vue.createApp({
    components: {},
    data(){
        return {
            items: [],
            itemCopy: {
                id: 0,
                urlOld: '',
                urlNew: '',
                type: 302
            },
            pagination: {
                current: 1,       // Current page
                total: 10,         // Items total count
                itemsPerPage: 5   // Items per page
            },
            value: 1,
            eachSide: 3,
            filterNew: '',
            filterOld: ''
        };
    },
    computed: {
        total(){
            return Math.ceil(this.filterList.length / 10);
        },
        firstPage(){
            return 1;
        },
        lastPage(){
            return this.total;
        },
        onFirstPage(){
            return this.currentPage === this.firstPage;
        },
        onLastPage(){
            return this.currentPage === this.lastPage;
        },
        currentPage(){
            if(this.value > this.total){
                return 1;
            }
            return this.value;
        },
        paginators(){
            var paginators = [];
            if (this.lastPage < this.eachSide * 2 + 4) {
                for (let i = this.firstPage; i < this.lastPage + 1; ++i) {
                    var classActive = '';
                    if(i === this.currentPage){
                        classActive = ' is-current';
                    }
                    paginators.push({
                        value: i,
                        class: 'pagination-link' + classActive
                    });
                }
            } else {
                if (this.currentPage - this.firstPage < this.eachSide + 2) { // if currentPage near firstPage
                    for (let i = this.firstPage; i < Math.max(this.eachSide * 2 + 1, this.currentPage + this.eachSide + 1); ++i) {
                        var classActive = '';
                        if(i === this.currentPage){
                            classActive = ' is-current';
                        }
                        paginators.push({
                            value: i,
                            class: 'pagination-link' + classActive
                        });
                    }
                    paginators.push({
                        value: '...',
                        class: 'pagination-ellipsis'
                    });
                    paginators.push({
                        value: this.lastPage,
                        class: 'pagination-link'
                    });
                } else if (this.lastPage - this.currentPage < this.eachSide + 2) { // if currentPage near lastPage
                    paginators.push({
                        value: this.firstPage,
                        class: 'pagination-link'
                    });
                    paginators.push({
                        value: '...',
                        class: 'pagination-ellipsis'
                    });
                    for (let i = Math.min(this.lastPage - this.eachSide * 2 + 1, this.currentPage - this.eachSide); i < this.lastPage + 1; ++i) {
                        var classActive = '';
                        if(i === this.currentPage){
                            classActive = ' is-current';
                        }
                        paginators.push({
                            value: i,
                            class: 'pagination-link' + classActive
                        });
                    }
                } else { // if currentPage in the middle
                    paginators.push({
                        value: this.firstPage,
                        class: 'pagination-link'
                    });
                    paginators.push({
                        value: '...',
                        class: 'pagination-ellipsis'
                    });
                    for (let i = this.currentPage - this.eachSide; i < this.currentPage + this.eachSide + 1; ++i) {
                        var classActive = '';
                        if(i === this.currentPage){
                            classActive = ' is-current';
                        }
                        paginators.push({
                            value: i,
                            class: 'pagination-link' + classActive
                        });
                    }
                    paginators.push({
                        value: '...',
                        class: 'pagination-ellipsis'
                    });
                    paginators.push({
                        value: this.lastPage,
                        class: 'pagination-link'
                    });
                }
            }
            return paginators;
        },
        filterList(){
            var thisVue = this;
            var list = [];
            this.items.forEach(function(item, i, arr){
                var add = true;
                if(thisVue.filterOld !== '' && !item.urlOld.includes(thisVue.filterOld)){
                    add = false;
                }
                if(thisVue.filterNew !== '' && !item.urlNew.includes(thisVue.filterNew)){
                    add = false;
                }
                if(add){
                    list.push(item);
                }
            });
            return list;
        },
        listItems(){
            var list = [];
            var start = 10 * (this.currentPage - 1);
            var end = 10 * this.currentPage;
            this.filterList.forEach(function(item, i, arr){
                if(i >= start && i < end){
                    list.push(item);
                }
            });
            return list;
        }
    },
    methods: {
        itemEdit(item){
            var thisVue = this;
            thisVue.itemCopy.id = item.id;
            thisVue.itemCopy.urlOld = item.urlOld;
            thisVue.itemCopy.urlNew = item.urlNew;
            thisVue.itemCopy.type = item.type;
            item.edit = true;
        },
        nextPage () {
            this.setPage(this.currentPage + 1);
        },
        prevPage () {
            this.setPage(this.currentPage - 1);
        },
        setPage (targetPage) {
            if (targetPage <= this.lastPage && targetPage >= this.firstPage) {
                this.value = targetPage;
            }
        }
    },
    mounted(){
        axios.get('/local/modules/vg.custom_redirect/admin/controllers/getRedirects.php').then(response => {
            this.items = response.data;
            axios.get('/local/modules/vg.custom_redirect/admin/controllers/getRedirects.php?full=1').then(response => {
                this.items = response.data;
            });
        });
        /*this.hash = window.location.hash.substr(1);
        
        axios.get('/local/modules/vg.treetables/admin/controllers/menu.php').then(response => {
            this.menu = response.data;
        });
        axios.get('/local/modules/vg.treetables/admin/controllers/routes-new.php').then(response => {
            this.items = response.data;
            this.findItemByPath(this.items, this.path);
            this.loader = false;
        });*/
    }
});
app.mount('#redirect-list');