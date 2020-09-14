import { Song } from '~logic/Song';
import { Link } from 'react-router-dom';
import React from 'react';

import style from './index.module.styl';

interface SongBlockProps {
    song: Song;
    currentlyPlaying: boolean;
    playSong: (song: Song) => void;
    className?: string;
}

const Index = (props: SongBlockProps) => {
    return (
        <div
            onClick={() => props.playSong(props.song)}
            className={`${props.className} ${style.songBlock} ${
                props.currentlyPlaying ? style.playing : ''
            }`}
            tabIndex={1}
        >
            <img src={props.song.art} alt='Album Cover' />
            <div className={style.details}>
                <div className={style.title}>{props.song.title}</div>
                <div className={style.bottom}>
                    <Link
                        to={`/album/${props.song.album.id}`}
                        onClick={(e) => {
                            e.stopPropagation();
                        }}
                    >
                        {props.song.album.name}
                    </Link>
                    <Link
                        to={`/artist/${props.song.artist.id}`}
                        onClick={(e) => {
                            e.stopPropagation();
                        }}
                    >
                        {props.song.artist.name}
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default Index;