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


var FilterableSongTable = React.createClass({
    getInitialState: function() {
        return {
            items: this.props.songs || []
        };
    },

    componentDidMount: function() {
        this.serverRequest = $.get('/get', function (result) {
            this.setState({
                items: result.items
            });
        }.bind(this));
    },

    componentWillUnmount: function() {
        this.serverRequest.abort();
    },

    render: function() {
        return (
            <SongTable songs={this.state.items} />
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
