import request from '../../mixins/base';

const initialState = {
    searchIndex: [],
};

const state = { ...initialState };

const actions = {
    buildIndex(context, stale = false) {
        if (stale) {
            context.commit('RESET_STATE');
        }

        request.methods
            .request()
            .get('/api/search/posts')
            .then(({ data }) => {
                context.commit('UPDATE_INDEX', data);
            });

    },
};

const mutations = {
    UPDATE_INDEX(state, data) {
        state.searchIndex.push(...data);
    },

    RESET_STATE(state) {
        state.searchIndex = [];
    },
};

const getters = {

};

export default {
    namespaced: true,
    state,
    actions,
    mutations,
    getters,
};
