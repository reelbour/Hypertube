const torrentStream = require('torrent-stream');
const fs = require('fs');
const pump = require('pump');
const rimraf = require("rimraf");

function magnetUrl (req, res, torrentLink) {
  let opts =
  {
    connections: 100,         // Max amount of peers to be connected to.
    uploads: 10,              // Number of upload slots.
  	tmp: '/tmp',              // Root folder for the files storage.
                              // Defaults to '/tmp' or temp folder specific to your OS.
                              // Each torrent will be placed into a separate folder under /tmp/torrent-stream/{infoHash}
  	path: '../public/film/' + torrentLink +'_tmp',   // Where to save the files. Overrides `tmp`.
  	verify: true,             // Verify previously stored data before starting
                              // Defaults to true
  	dht: true,                // Whether or not to use DHT to initialize the swarm.
                              // Defaults to true
  	tracker: true,            // Whether or not to use trackers from torrent file or magnet link
                              // Defaults to true
  	trackers: [],             // Allows to declare additional custom trackers to use
                              // Defaults to empty
  }
  let engine = torrentStream(torrentLink, opts);
  console.log('Here2 !');
  engine.on('ready', () => {
    engine.files.forEach(function (file) {
      console.log('Here3 !');
        let fullPath = '../public/film/'  + torrentLink + ".mp4";
        fs.exists(fullPath, (exists) => {
          if (exists) {
            let sizeOfDownloaded = fs.statSync(fullPath).size;
            let sizeInTorrent = file.length;

            if (sizeOfDownloaded === sizeInTorrent) {

              // rimraf('../public/film/' + id + '_tmp',function (err) {
              //   if (err) throw err;
              //   // if no error, file has been deleted successfully
              //   console.log('File deleted!');
              // });

              const pathToVideo = '../public/film/' + torrentLink + ".mp4";
              let fileSize = file.length;
              const range = req.headers.range;

              let start = 0;
              let end = fileSize - 1;
              //Simon ffmpeg ?
              if (range)
                partialContent(req, res, start, end, fileSize, file);
              else
                notPartialContent(req, res, fileSize, pathToVideo);
            }
            else
              downloadAndStream(req, res, file, fullPath);
        }
        else {
          downloadAndStream(req, res, file, fullPath);
        }
      });
    });
  })
};

function downloadAndStream (req, res, file, fullPath) {
  console.log('Here !', fullPath);

  let videoFormat = file.name.split('.').pop();
  if (videoFormat === 'mp4' || videoFormat === 'mkv' || videoFormat === 'ogg' || videoFormat === 'webm') {
    let currentStream = file.createReadStream();
    currentStream.pipe(fs.createWriteStream(fullPath));

    const pathToVideo = fullPath;
    let fileSize = file.length;
    const range = req.headers.range;
    let start = 0;
    let end = fileSize - 1;

    if (range) {
        partialContent(req, res, start, end, fileSize, file);
    } else {
        notPartialContent(req, res, fileSize, pathToVideo);
    }
  }
};

function partialContent (req, res, start, end, fileSize, file) {
  let range = req.headers.range;
  let parts = range.replace(/bytes=/, '').split('-');
  let newStart = parts[0];
  let newEnd = parts[1];
  start = parseInt(newStart, 10);

  if (!newEnd) {
      end = start + 100000000 >= fileSize ? fileSize - 1 : start + 100000000;
  }
  else
      end = parseInt(newEnd, 10);
  let chunksize = end - start + 1;
  let head = {
      'Content-Range': 'bytes ' + start + '-' + end + '/' + fileSize,
      'Accept-Ranges': 'bytes',
      'Content-Length': chunksize,
      'Content-Type': 'video/mp4',
      'Connection': 'keep-alive'
  };
  res.writeHead(206, head);

  let stream = file.createReadStream({
      start: start,
      end: end
  });
  pump(stream, res);
};

function notPartialContent (req, res, fileSize, pathToVideo) {
  const head = {
      'Content-Length': fileSize,
      'Content-Type': 'video/mp4',
  };
  fs.createReadStream(pathToVideo).pipe(res);
  res.writeHead(200, head);
};

module.exports = {
 magnetUrl:         magnetUrl,
 // partialContent:    partialContent,
 // notPartialContent: notPartialContent
};
