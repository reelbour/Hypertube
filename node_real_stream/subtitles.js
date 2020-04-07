const http = require('http');
const fs = require('fs');
const srt2vtt = require('srt-to-vtt');
const download = require('download-file');
const OS = require('opensubtitles-api');
const OpenSubtitles = new OS({
    useragent: 'Hypertube_19',
    username: 'RaziaBot',
    password: 'hypertube',
    ssl: false
})

function getSubtitles (res, id, lang) {
   console.log("subtitles id: ", id, "lang: ", lang);
   let idlang = convertLang(lang);
   OpenSubtitles.search({
       sublanguageid: idlang,
       imdbid: id,
   }).then(subtitles => {
       console.log('Subtitles found ! ', subtitles)
       convertSubtitles(res, subtitles, id, lang);
   }).catch((err) => {
       console.log('Here: ', err);
   });
};

function convertSubtitles(res, subtitles, id, lang) {
   console.log('Here !!!');
  let dirname = '../public/subtitles/';
  let filename = id + '_' + lang + '.srt';
  fs.exists(dirname + filename, (exists) => {
    console.log('Here too !!!');
    if(exists){
      console.log(filename);
      fs.createReadStream(dirname + filename)
        .pipe(srt2vtt())
        .pipe(res);
    } else if (subtitles[lang]) {
      let url = subtitles[lang].url;
      let options = {
        directory: dirname,
        filename: filename,
        extensions: ['srt']
      };
      console.log(url);
      download(url, options, function (err) {
        if (err)
          console.log('error: ', err);
        fs.createReadStream(dirname + filename)
          .pipe(srt2vtt())
          .pipe(res);
        })
      }
  });
};

function convertLang(lang){
  switch (lang) {
    case 'fr':
      return 'fre';
    case 'en':
      return 'eng';
    case 'ru':
      return 'rus';
    case 'ar':
      return  'ara';
    case 'zh':
      return  'chi';
    case 'br':
      return  'bre';
    case 'eo':
      return  'epo';
    case 'de':
      return  'ger';
    case 'it':
      return  'ita';
    case 'ja':
      return  'jpn';
    case 'ko':
      return  'kor';
    case 'pt':
      return  'por';
    case 'sv':
      return  'swe';
    case 'es':
      return  'spa';
    case 'vi':
      return  'vie';
    case 'pb':
      return  'pob';
  }
};

module.exports = {
 getSubtitles:         getSubtitles,
};
