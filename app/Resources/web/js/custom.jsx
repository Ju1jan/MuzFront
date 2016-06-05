var $storage = $('#storage'),
    artistsCount = $storage.data('artists-count'),
    countriesCount = $storage.data('countries-count'),
    ajaxUrl = $storage.data('ajax-url');

var Url = {
    params: {},

    init: function () {
        let i,
            prm,
            params = window.location.search.substring(1).split('&');
        for (i = 0; i < params.length; i++) {
            prm = params[i];
            let pair = prm.split("="), key = pair[0], value = pair[1];
            // assumption: we don't have arrays in url parameters
            Url.params[key] = decodeURIComponent(value);
        }
        return this;
    },

    paramsQuery: function () {
        let key, value, i = 0, query = '';
        for (key in Url.params) {
            if (!key || 'undefined' === typeof key) continue;
            value = Url.params[key];
            if ('undefined' === typeof value) continue;
            query += (i++ ? '&' : '?') + key + '=' + value;
        }
        return query;
    },

    update: function () {
        let url = window.location.pathname + Url.paramsQuery();
        window.history.pushState("MuzFront", "Title", url);
        return this;
    },

    // TODO: check if does it work and update or rm :))
    // NB: we're able to get param lin this way: (Url.param.sort), where 'sort' is name of param
    getParam: function (key, def) {
        if ('undefined' == typeof(Url.params[key])) {
            return Url.params[key];
        }
        return def;
    },

    setParam: function (key, value) {
        Url.params[key] = value;
        return this;
    }
};


Url.init();

var SongRow = React.createClass({
    render: function () {
        return (
            <tr>
                <td>{this.props.song.artist}</td>
                <td>{this.props.song.song}</td>
                <td>{this.props.song.country}</td>
                <td>{this.props.song.genre}</td>
                <td>{this.props.song.year}</td>
            </tr>
        );
    }
});


var PaginationRow = React.createClass({
    render: function () {
        let i,
            pages = [],
            pagePrev,
            pageNext,
            songTable = this.props.songTable,
            pagination = this.props.pagination;

        for (i = 1; i < pagination.pageLast + 1; i++) {
            pages.push({
                id: i,
                link: '?page=' + i,
                current: (i == pagination.pageCurrent)
            });
        }

        // TODO: implement this
        pagePrev = pagination.pageCurrent - 1;
        pageNext = pagination.pageCurrent + 1;
        if (pagePrev < 1) { pagePrev = false;}
        if (pageNext > pagination.pageLast) {pageNext = false;}

        return (
            <ul className="pagination pages">
                <li>
                    <span aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </span>
                </li>
                {pages.map(function (pg) {
                    return (
                        <li onClick={() => songTable.handlePageClick(pg.id) }
                            className={pg.current ? 'active' : ''} key={pg.id}>
                            <span>{pg.id}</span>
                        </li>);
                })}
                <li>
                    <span aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </span>
                </li>
            </ul>
        );
    }
});


var FilterableSongTable = React.createClass({
    getInitialState: function () {
        return {
            items: this.props.songs || [],
            pagination: {
                pageCurrent: 0,
                pageLast: 0,
                itemFirst: null,
                itemLast: null,
                itemsAll: 0,
                itemsShowed: 0
            }
        };
    },

    loadServerData: function () {
        console.log('loadServerData init...');
        $('#dynamicContent').addClass('loading');
        if ('undefined' !== typeof this.serverRequest) {
            this.serverRequest.abort(); // To stop previous request if exist
        }

        // TODO: use post request
        this.serverRequest = $.get(ajaxUrl + Url.paramsQuery(), function (response) {
            if ('string' !== typeof(response.msg) || 'OK' !== response.msg) {
                console.error('Oops! Response does not have any data', response);
                return false;
            }
            this.setState({
                items: response.items,
                pagination: response.pagination
            });
        }.bind(this))
            .done(function() {
                console.info('...loadServerData ready.');
            })
            .fail(function(e) {
                console.error('Oops! Ajax request failed', e);
            })
            .always(function () {
                $('#dynamicContent').removeClass('loading');
            });
    },

    componentDidMount: function () {
        window.updateDynamicTable = this.handleExternal;
        this.loadServerData();
    },

    componentWillUnmount: function () {
        this.serverRequest.abort();
    },

    updateTable: function () {
        Url.update();
        this.loadServerData();
    },

    handlePageClick: function (page) {
        Url.setParam('page', page);
        this.updateTable();
    },

    handleCountClick: function (count) {
        // count of items per page
        Url.setParam('showed', count);
        this.updateTable();
    },

    handleThClick: function (headerField) {
        let orderBy  = Url.params.sort || false;

        if (headerField === orderBy) {
            let orderDir = Url.params.dir  || 'DESC';
            orderDir = ('DESC' === orderDir) ? 'ASC' : 'DESC';
            Url.setParam('dir', orderDir);
        } else {
            Url.setParam('sort', headerField);
        }

        this.updateTable();
    },

    handleExternal: function (field, value) {
        console.log('FilterableSongTable :: External handler', field, value);
        Url.setParam(field, value).setParam('page', 1);
        this.updateTable();
    },

    render: function () {
        let table = this,
            rows = [],
            items = this.state.items,
            counts = [10, 25, 50], // each element is possible count of items per page
            pagination = this.state.pagination;

        let sortableIcons = function (headerField) {
            let orderBy  = Url.params.sort || false,
                orderDir = Url.params.dir  || 'DESC',
                cls = 'glyphicon glyphicon-resize-vertical';

            if (headerField === orderBy) {
                cls = 'glyphicon ';
                cls += ('DESC' === orderDir) ? 'glyphicon-sort-by-attributes-alt' : 'glyphicon-sort-by-attributes';
            }

            return (
                <span className={cls}></span>
            );
        };

        items.forEach(function (song) {
            rows.push(<SongRow song={song} key={song.id}/>);
        });

        return (
            <section>
                <table className="table table-striped table-responsive">
                    <thead>
                    <tr>
                        <th onClick={() => this.handleThClick('artist') }>
                            Artist { sortableIcons('artist') }
                        </th>
                        <th onClick={() => this.handleThClick('song') }>
                            Song { sortableIcons('song') }
                        </th>
                        <th onClick={() => this.handleThClick('country') }>
                            Country { sortableIcons('country') }
                        </th>
                        <th onClick={() => this.handleThClick('genre') }>
                            Genre { sortableIcons('genre') }
                        </th>
                        <th onClick={() => this.handleThClick('year') }>
                            Year { sortableIcons('year') }
                        </th>
                    </tr>
                    </thead>
                    <tbody>{rows}</tbody>
                </table>
                <nav className="pagination-row">
                    <ul className="pagination counts pull-right">
                        <li><span>Items on page:</span></li>
                        {counts.map(function (count) {
                            return (
                                <li onClick={() => table.handleCountClick(count) }
                                    className={count === pagination.itemsOnPage ? 'active' : ''} key={count}>
                                    <span>{count}</span>
                                </li>
                            );
                        })}
                    </ul>
                    <PaginationRow pagination={pagination} songTable={this}/>
                </nav>
                <div className="in-progress">
                    <p>
                        <span className="glyphicon glyphicon-refresh rotating"></span>
                    </p>
                    <p className="text-center">Loading...</p>
                    <p className="text-center">
                        The table is being built using songs from {countriesCount} countries and {artistsCount} artists.
                    </p>
                </div>
            </section>
        );
    }
});

let $filter = $('#filter');

$filter.find('select').on('change', function () {
    let name = $(this).attr('name');
    if ('function' === typeof window.updateDynamicTable) {
        updateDynamicTable(name, this.value);
    } else {
        console.warn('Oops. Alias for external method does not exist');
    }

    $filter.find('div.filter-block').show();

    // It does not have sense to select artist and country both at the same time
    let $selectBox;
    if (!!parseInt(Url.params.aid)) {
        console.log('hide cid');
        $selectBox = $filter.find('select[name="cid"]');
        Url.setParam('cid', 0);
    } else if (!!parseInt(Url.params.cid)) {
        console.log('hide aid');
        $selectBox = $filter.find('select[name="aid"]');
        Url.setParam('aid', 0);
    }

    if (!!$selectBox && $selectBox.length) {
        $selectBox.closest('div.filter-block').slideUp();
    }
});

ReactDOM.render(
    <FilterableSongTable songs={[]}/>,
    document.getElementById('dynamicContent')
);
