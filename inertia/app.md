```js
import Vue from 'vue'

// Register all the Vue components
const files = require.context('./', true, /\.vue$/i)
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Boot the current Vue component
const root = document.getElementById('app')
window.vue = new Vue({
    render: h => h(
        Vue.component(root.dataset.component), {
            props: JSON.parse(root.dataset.props)
        }
    )
}).$mount(root)

render(h) {
    return h(this.page.component, { props: this.page.props })
}

```