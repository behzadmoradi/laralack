<template>
    <ul>
        <div>Channels</div>
        <li v-for="channel in channels" :key=channel.id>
            {{channel.name}}
        </li>
    </ul>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }, 
        data() {
            return {
                channels: []
            }
        },
        created() {
            axios.get('http://127.0.0.1:8000/api/v1/channels/1?api_token=B1zcpYYe96Ycm8f3NeW5MAUYaNxgL5IetDXqvYOBGxFJvV24FAw8hoGCIbfGpNTbmh6yaFhMLxnbaBwI').then(response => {
                this.channels = response.data['data']['channels'];
            })
        },
    }
</script>
