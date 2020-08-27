Nova.booting((Vue, router, store) => {
    Vue.component('index-expiration', require('./components/IndexField'))
    Vue.component('detail-expiration', require('./components/DetailField'))
})
