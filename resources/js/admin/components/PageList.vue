<template>
    <div class="page-list">
        <errors v-if="errors" :errors="errors"></errors>

        <header class="table-control-header mb-2">
            <div class="num-selected total" v-if="totalPages > pages.length">{{ totalPages }} Total Pages</div>
            <div class="num-selected">
                {{pages.length}} pages
            </div>
            <div class="num-selected">
                <input v-model="searchTerm" type="text" placeholder="Search pages...">
            </div>
        </header>

        <div class="table-responsive">
                <table class="table table-striped page-table">
                        <thead class="thead-light">
                            <th @click="sort('name')" scope="col" class="cell-name">
                                Name
                                <a v-if="currentSort==='name' && currentSortDir==='asc'" >▴</a>
                                <a v-else-if="currentSort==='name' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('slug')" scope="col" class="cell-slug">
                                Slug
                                <a v-if="currentSort==='slug' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='slug' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('parent')" scope="col" class="cell-parent">
                                Parent
                                <a v-if="currentSort==='parent' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='parent' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th scope="col" class="cell-hotspots">
                                Hotspots
                            </th>
                            <th @click="sort('hide_from_menu')" scope="col" class="cell-hidden">
                                Visible
                                <a v-if="currentSort==='hide_from_menu' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='hide_from_menu' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th @click="sort('created_at')" scope="col" class="cell-date">
                                Created At
                                <a v-if="currentSort==='created_at' && currentSortDir==='asc'">▴</a>
                                <a v-else-if="currentSort==='created_at' && currentSortDir==='desc'">▾</a>
                            </th>
                            <th scope="col" class="cell-buttons"></th>
                        </thead>
                        <tbody>
                            <tr v-for="page in sortedPages" :key="page.id">
                                <td class="cell-name">
                                    <a :href="pageEditURL(page)" title="Edit">{{ page.name }}</a>
                                </td>
                                <td class="cell-slug">
                                    {{ page.slug }}
                                </td>
                                <td class="cell-parent">
                                    {{ page.parent ? page.parent.name : '-' }}
                                </td>
                                <td class="cell-hotspots">
                                    <a :href="pageHotspotsURL(page)" title="Edit Hotspots">
                                        {{ page.hotspots_count || 0 }}
                                    </a>
                                </td>
                                <td class="cell-hidden">
                                    <span v-if="!page.hide_from_menu" class="badge badge-pill badge-success">Visible</span>
                                    <span v-else class="badge badge-pill badge-secondary">Hidden</span>
                                </td>
                                <td class="cell-date">
                                    {{ page.created_at | dateFormat }}
                                </td>
                                <td class="text-right button-cell">
                                    <a :href="pageEditURL(page)"
                                    class="btn btn-primary btn-sm">Edit</a>
                                <button type="button"
                                    class="btn btn-danger btn-sm"
                                    @click="deleteSinglePageClick(page)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "PageList",
    props: {
        initialPages: {
            type: Array,
            required: true,
        },
        totalPages: {
            type: Number,
            required: true,
        },
        storeId: {
            type: Number,
            required: true,
        }
    },
    data() {
        return {
            pages: this.initialPages,
            searchTerm: '',
            currentSort: 'name',
            currentSortDir: 'asc',
            errors: []
        };
    },
    computed: {
        sortedPages() {
            let sorted = this.pages.filter((page) => {
                return page.name.toLowerCase().includes(this.searchTerm.toLowerCase()) ||
                       page.slug.toLowerCase().includes(this.searchTerm.toLowerCase());
            });

            sorted.sort((a, b) => {
                let aVal = a[this.currentSort];
                let bVal = b[this.currentSort];

                if (aVal === null || aVal === undefined) aVal = '';
                if (bVal === null || bVal === undefined) bVal = '';

                if (this.currentSortDir === 'asc') {
                    return aVal > bVal ? 1 : -1;
                } else {
                    return aVal < bVal ? 1 : -1;
                }
            });

            return sorted;
        }
    },
    methods: {
        sort(col) {
            if (col === this.currentSort) {
                this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
            } else {
                this.currentSort = col;
                this.currentSortDir = 'asc';
            }
        },
        pageEditURL(page) {
            return `/admin/store/${this.storeId}/pages/${page.id}`;
        },
        pageHotspotsURL(page) {
            return `/admin/store/${this.storeId}/pages/${page.id}/hotspots`;
        },
        async deleteSinglePageClick(page) {
            if (!confirm(`Delete "${page.name}"?`)) return;

            try {
                const response = await axios.delete(`/admin/store/${this.storeId}/pages/${page.id}/delete`);
                if (response.data.success) {
                    this.pages = this.pages.filter(p => p.id !== page.id);
                    this.$toast.success('Page deleted');
                }
            } catch (error) {
                this.errors = ['Error deleting page'];
            }
        }
    }
};
</script>

<style scoped lang="scss">
.page-list {
    .table-control-header {
        display: flex;
        gap: 1rem;
        align-items: center;
        margin-bottom: 1rem;

        input {
            flex: 1;
            max-width: 300px;
        }
    }

    .page-table {
        .cell-name {
            min-width: 200px;
        }
        .cell-slug {
            min-width: 150px;
        }
        .cell-parent {
            min-width: 150px;
        }
        .cell-hotspots {
            text-align: center;
            width: 100px;
        }
        .cell-hidden {
            text-align: center;
            width: 120px;
        }
        .cell-date {
            width: 160px;
        }
        .cell-buttons {
            text-align: right;
            width: 180px;
        }
    }
}
</style>
