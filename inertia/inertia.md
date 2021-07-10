## Error
422 error for errors

```js
<script>
export default {
    inject: ['page'],
    props: {
        name: string,
    },
    data() {
        return {
            title: null,
        }
    },
    methods: {
        submit() {
        },
    }
}
</script>
```

## Inertia link
> By default link "push" a new history state, and reset the scroll position back to the top of the page.
```html
// replace : history
// preserve-scroll : keep scroll position
<inertia-link 
    :href="route('users.create')" 
    :class="{ active: route().current('users') }"
    replace
    preserve-scroll
>Home</inertia-link>
```

```js
// Redirect
Inertia.visit(url, {
  replace: true,
  preserveScroll: true,
})
```