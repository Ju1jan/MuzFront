var SongRow = React.createClass({
    render: function() {
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

var SongTable = React.createClass({
    render: function() {
        var rows = [];

        this.props.songs.forEach(function(song) {
            rows.push(<SongRow song={song} key={song.id} />);
        });
        return (
            <table className="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Artist</th>
                    <th>Song</th>
                    <th>Genre</th>
                    <th>Year</th>
                </tr>
                </thead>
                <tbody>{rows}</tbody>
            </table>
        );
    }
});

var PaginationRow = React.createClass({
    render: function() {
        var i,
            pages = [],
            pagination = this.props.pagination;

        for (i =1; i < pagination.pageLast + 1; i++) {
            pages.push({
                id: i,
                link: '?page=' + i,
                value: i,
                current: (i == pagination.pageCurrent)
            });
        }

        return (
            <nav>
                <ul className="pagination">
                    <li>
                        <a href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    {pages.map(function(pg) {
                        return (
                            <li className={pg.current ? 'active' : ''} key={pg.id}>
                                <a href={pg.link}>{pg.value}</a>
                            </li>);
                    })}

                    <li>
                        <a href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        );
    }
});


var FilterableSongTable = React.createClass({
    getInitialState: function() {
        return {
            items: this.props.songs || [],
            pagination: {
                pageCurrent: 0,
                pageLast:    0,
                itemFirst:   null,
                itemLast:    null,
                itemsAll:    0,
                itemsShowed: 0
            }
        };
    },

    componentDidMount: function() {
        this.serverRequest = $.get('/get', function (result) {
            this.setState({
                items: result.items,
                pagination: result.pagination,
            });
        }.bind(this));
    },

    componentWillUnmount: function() {
        this.serverRequest.abort();
    },

    render: function() {
        var rows = [],
            items = this.state.items,
            pagination = this.state.pagination;

        items.forEach(function(song) {
            rows.push(<SongRow song={song} key={song.id} />);
        });

        return (
            <section>
                <table className="table table-striped table-responsive">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Artist</th>
                        <th>Song</th>
                        <th>Genre</th>
                        <th>Year</th>
                    </tr>
                    </thead>
                    <tbody>{rows}</tbody>
                </table>
                <div className="pagination-row">
                    <PaginationRow pagination={pagination} />
                </div>
            </section>
        );
    }
});


var SONGS = [
    {id: 1, year: 1989, artist: 'Whatever', song: 'SongTitle', genre: 'Reggae'},
    {id: 2, year: 2006, artist: 'Any', song: 'Qwerty', genre: 'TycTycTyc'}
];

ReactDOM.render(
    <FilterableSongTable songs={SONGS} />,
    document.getElementById('dynamicContent')
);
