<template>
    <section>
        <div v-if="isReady && posts?.length > 0" class="mt-5">
                <div>
                    <h4 class="my-4 border-bottom mt-5 pb-2">
                        <span class="border-bottom border-dark pb-2">Pinned Posts</span>
                    </h4>
                    <div :key="`${index}-${post.id}`" v-for="(post, index) in posts">
                        <router-link :to="{ name: 'show-post', params: { slug: post.slug } }"
                            class="text-decoration-none">
                            <div class="card mb-4 shadow">
                                <div class="card-body px-0">
                                    <div class="container d-lg-inline-flex align-items-center">
                                        <div v-if="post.featured_image" class="col-12 col-lg-3 p-0">
                                            <img :src="post.featured_image" :alt="post.featured_image_caption"
                                                class="rounded w-100" />
                                        </div>
                                        <section class="col-12 mt-3 mt-lg-0 px-0 px-lg-3"
                                            :class="post.featured_image ? 'col-lg-9' : ''">
                                            <h5 class="card-title text-truncate mb-0">{{ post.title }}</h5>
                                            <p class="card-text text-truncate">{{ post.summary }}</p>
                                            <p class="card-text mb-0 text-secondary">
                                                {{ post.user.name }}
                                                <span v-if="post.topic.length"> in {{ post.topic[0].name }} </span>
                                            </p>
                                            <p class="card-text text-secondary">
                                                {{ moment(post.published_at).format('MMM D, Y') }} —
                                                {{ post.read_time }}
                                            </p>
                                        </section>
                                    </div>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
    </section>
</template>

<script>
import NProgress from 'nprogress';

export default {
    name: 'pinned-posts-component',

    data() {
        return {
            posts: [],
            isReady: false,
        };
    },

    async created() {
        await Promise.all([this.fetchPinnedPosts()]);
        this.isReady = true;
        NProgress.done();
    },

    methods: {
        fetchPinnedPosts() {
            return this.request()
                .get('/api/pinned-posts')
                .then(({ data }) => {
                    this.posts = data;
                    NProgress.inc();
                })
                .catch(() => {
                    NProgress.done();
                });
        },
    },
};
</script>
