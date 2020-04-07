const path = require('path');
const fs = require('fs');
const express = require('express');
const request = require('request');
const app = express();
const stream = require('./stream');
const subtitles = require('./subtitles');

app.use(express.static(path.join(__dirname, '../public')));

app.use(function (req, res, next) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');
    res.setHeader('Access-Control-Allow-Headers', '"Origin, X-Requested-With, Content-Type, Accept"');
    res.setHeader('Access-Control-Allow-Credentials', true);
    res.setHeader('content-type', 'text/vtt');
    next();
});

let torrentHash = {};
let currentMovieUrl = '';
let currentIMDB = '';

app.get('/', function (req, res) {
  res.send('index.php')
})


app.get('/stream/:hash', function (req, res) {
  console.log('Stream: ', req.params.hash);
    let tmpReq = req;
    let hash =  req.params.hash

    // request('https://tv-v2.api-fetch.website/movie/' + id, function (req, res) {
    //     if (res.body) {
    //         let movieInfo = JSON.parse(res.body);
    //         if (movieInfo.torrents.en) {
    //             currentMovieUrl = movieInfo.torrents.en[quality].url;
    //             currentIMDB = movieInfo.imdb_id;
    //             torrentHash[tmpReq.params.id] = {
    //                 'url':movieInfo.torrents.en[quality].url,
    //                 'imdb': movieInfo.imdb_id
    //             };
    //         }
    //     }
    // });
    setTimeout(function () {
        if (hash) {
            console.log('Here !');
            stream.magnetUrl(req, res, hash);
        }
        else {
            res.send("error");
        }
    }, 1000);
});

app.get('/subtitles/:id/:lang', function (req, res){
  let tmpReq = req;
  console.log('subtitles id: ',req.params.id, 'lang: ', req.params.lang);
  setTimeout(function() {
    subtitles.getSubtitles(res, tmpReq.params.id, tmpReq.params.lang);
  }, 2000);
});

app.listen(3000, function () {
    console.log('Listening on port ' + '3000!')
});





// app.get('/subtitles/en/:id', function (req, res) {
//     let tmpReq = req;
//     request('https://tv-v2.api-fetch.website/movie/' + req.params.id, function (req, res) {
//         if (res.body) {
//             let movieInfo = JSON.parse(res.body);
//             currentIMDB = movieInfo.imdb_id;
//         }
//     });
//     setTimeout(function() {
//         subtitles.getEnglishSubtitles(res, currentIMDB, tmpReq.params.id);
//     }, 2000);
// });
