Nova.booting((Vue, router, store) => {
    Vue.component('index-badge', require('./components/IndexField'))
    Vue.component('detail-badge', require('./components/DetailField'))
})
