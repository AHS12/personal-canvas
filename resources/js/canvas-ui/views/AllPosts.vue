<template>
    <section>
        <page-header />

        <div v-if="isReady" class="mt-5">
            <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1 col-md-12 mt-3">
                <h1 class="font-serif">Azizul's Canvas</h1>
                <p class="lead text-secondary">Welcome to "My Personal Canvas,"
                    a blog where I bring to life the vivid imagery of my mind through writings!</p>

                <pinned-posts />
                <main role="main" class="mt-5">
                    <div>

                        <h4 class="my-4 border-bottom mt-5 pb-2">
                            <span class="border-bottom border-dark pb-2">Posts</span>
                        </h4>
                        <div class="row row-cols-lg-2 row-cols-md-3 row-cols-sm-2">


                        <div class="col mb-4" :key="`${index}-${post.id}`" v-for="(post, index) in posts">
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

                        <infinite-loading spinner="spiral" @infinite="fetchPosts">
                            <span slot="no-more" />
                            <div slot="no-results" class="text-left">
                                <div class="my-5">
                                    <p class="lead text-center text-muted mt-5">You have no published posts</p>
                                    <p class="lead text-center text-muted mt-1">
                                        Write on the go with our mobile-ready app!
                                    </p>
                                </div>
                            </div>
                        </infinite-loading>
                    </div>
                </main>
            </div>
        </div>
    </section>
</template>

<script>
import InfiniteLoading from 'vue-infinite-loading';
import NProgress from 'nprogress';
import PageHeader from '../components/PageHeaderComponent';
import PinnedPosts from '../components/PinnedPosts';
import isEmpty from 'lodash/isEmpty';

export default {
    name: 'all-posts',

    components: {
        InfiniteLoading,
        PageHeader,
        PinnedPosts,
    },

    metaInfo() {
        return {
            title: "Azizul's Canvas",
        };
    },

    data() {
        return {
            page: 1,
            posts: [],
            isReady: false,
        };
    },

    async created() {
        await Promise.all([this.fetchPosts()]);
        this.isReady = true;
        NProgress.done();
    },

    methods: {
        fetchPosts($state) {
            if ($state) {
                return this.request()
                    .get('/api/posts', {
                        params: {
                            page: this.page,
                        },
                    })
                    .then(({ data }) => {
                        if (!isEmpty(data) && !isEmpty(data.data)) {
                            this.page += 1;
                            this.posts.push(...data.data);

                            $state.loaded();
                        } else {
                            $state.complete();
                        }

                        if (isEmpty($state)) {
                            NProgress.inc();
                        }
                    })
                    .catch(() => {
                        NProgress.done();
                    });
            }
        },
    },
};
</script>
