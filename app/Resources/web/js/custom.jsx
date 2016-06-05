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
                <td>{this.props.song.id}</td>
                <td>{this.props.song.artist}</td>
                <td>{this.props.song.song}</td>
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
        this.serverRequest = $.get('/get' + Url.paramsQuery(), function (result) {
            $('#dynamicContent').removeClass('loading');
            console.info('...loadServerData ready.');
            this.setState({
                items: result.items,
                pagination: result.pagination,
            });
        }.bind(this));
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

    handleThClick: function (sortBy) {
        Url.setParam('sort', sortBy);
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

        items.forEach(function (song) {
            rows.push(<SongRow song={song} key={song.id}/>);
        });

        return (
            <section>
                <table className="table table-striped table-responsive">
                    <thead>
                    <tr>
                        <th onClick={() => this.handleThClick('id') }>#</th>
                        <th onClick={() => this.handleThClick('artist') }>Artist</th>
                        <th onClick={() => this.handleThClick('song') }>Song</th>
                        <th onClick={() => this.handleThClick('genre') }>Genre</th>
                        <th onClick={() => this.handleThClick('year') }>Year</th>
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
                <p className="in-progress">
                    <span className="glyphicon glyphicon-refresh rotating"></span>
                </p>
            </section>
        );
    }
});

$('#filter').find('select').on('change', function () {
    var $name = $(this).attr('name');
    if ('function' === typeof window.updateDynamicTable) {
        updateDynamicTable($name, this.value);
    } else {
        console.warn('Oops. Alias for external method does not exist');
    }
});

/*let SONGS = [
    {id: 1, year: 1989, artist: 'Whatever', song: 'SongTitle', genre: 'Reggae'},
    {id: 2, year: 2006, artist: 'Any', song: 'Qwerty', genre: 'TycTycTyc'}
];*/

ReactDOM.render(
    <FilterableSongTable songs={[]}/>,
    document.getElementById('dynamicContent')
);
