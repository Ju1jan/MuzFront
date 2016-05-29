<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionInitTables20160528182648 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        # HELPERS
        $addPrimaryKey = function (&$table) {
            $table->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true,));
            $table->setPrimaryKey(array('id'));
        };

        $addNameColumn = function (&$table, $name = 'name') {
            $table->addColumn($name, 'string', array('length' => 127));
        };

        # COUNTRIES
        $tblCountries = $schema->createTable('countries');
        $addPrimaryKey($tblCountries);
        $addNameColumn($tblCountries);
        $addNameColumn($tblCountries, 'native_name');
        $tblCountries->addColumn('code', 'string', array('length' => 7));

        # GENRES
        $tblGenres = $schema->createTable('genres');
        $addPrimaryKey($tblGenres);
        $addNameColumn($tblGenres);

        # ARTISTS
        $tblArtists = $schema->createTable('artists');
        $addPrimaryKey($tblArtists);
        $addNameColumn($tblArtists);
        $tblArtists->addColumn('country_id', 'integer',  ['unsigned' => true]);

        $tblArtists->addIndex(['country_id'], 'FK_artists_cross_country');
        $tblArtists->addForeignKeyConstraint('countries', ['country_id'], array('id'),
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ),
            'FK_artists_cross_country'
        );

        # SONGS
        $tblSongs = $schema->createTable('songs');
        $addPrimaryKey($tblSongs);
        $addNameColumn($tblSongs);
        $tblSongs->addColumn('artist_id', 'integer',  ['unsigned' => true]);
        $tblSongs->addColumn('genre_id', 'integer',  ['unsigned' => true]);

        $tblSongs->addIndex(['artist_id'], 'FK_songs_cross_artists');
        $tblSongs->addForeignKeyConstraint('artists', array('artist_id'), array('id'),
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ),
            'FK_songs_cross_artists'
        );

        $tblSongs->addIndex(['genre_id'], 'FK_songs_cross_genre');
        $tblSongs->addForeignKeyConstraint('genres', array('genre_id'), array('id'),
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ),
            'FK_songs_cross_genre'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('songs');
        $schema->dropTable('artists');
        $schema->dropTable('genres');
        $schema->dropTable('countries');
    }
}
