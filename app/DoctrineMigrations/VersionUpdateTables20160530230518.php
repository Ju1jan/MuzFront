<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class VersionUpdateTables20160530230518 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $tblSongs = $schema->getTable('songs');
        $tblSongs->removeForeignKey('FK_songs_cross_genre');
        $tblSongs->dropIndex('FK_songs_cross_genre');
        $tblSongs->dropColumn('genre_id');
        $tblSongs->addColumn('year', 'smallint',  ['unsigned' => true]);

        $tblArtists = $schema->getTable('artists');
        $tblArtists->addColumn('genre_id', 'integer',  ['unsigned' => true]);
        $tblArtists->addForeignKeyConstraint('genres', array('genre_id'), array('id'),
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ),
            'FK_artists_cross_genre'
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $tblSongs = $schema->getTable('songs');
        $tblSongs->dropColumn('year');
        $tblSongs->addColumn('genre_id', 'integer',  ['unsigned' => true]);
        $tblSongs->addIndex(['genre_id'], 'FK_songs_cross_genre');
        $tblSongs->addForeignKeyConstraint('genres', array('genre_id'), array('id'),
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            ),
            'FK_songs_cross_genre'
        );

        $tblArtists = $schema->getTable('artists');
        $tblArtists->removeForeignKey('FK_artists_cross_genre');
        $tblArtists->dropColumn('genre_id');
    }
}
