function callback(mutationsList, observer) {
    mutationsList.forEach(mutation => {
        if (mutation.attributeName === 'class') {
            console.log(document.body.classList);
            // @todo should be more dynamic 3dtXE will get killed on every single rebuild of the application
            if(document.body.classList.contains('Modal/modalOpen/3dtXF')) {
                //thingy modal is open 
                console.log("modal open");
                var moviemodal = document.getElementsByClassName('EpisodeDetailsModalContent/tabContent/lAMVG');
                if(moviemodal.length == 1) {
                    var episode = moviemodal[0];
                    var path = episode.getElementsByClassName('EpisodeSummary/path/12vRz')[1].innerHTML;
                    var httpRequest = new XMLHttpRequest();
                    httpRequest.addEventListener('load', function(event) {
                        if (httpRequest.status >= 200 && httpRequest.status < 300) {
                            var response = httpRequest.response;
                            var videlem = document.createElement("video");
                            videlem.classList.add('video-js');
                            videlem.setAttribute('controls',"");
                            videlem.setAttribute('preload','none');
                            videlem.setAttribute('data-setup','{}');
                            /// ... some setup like poster image, size, position etc. goes here...
                            /// now, add sources:
                            var sourceMP4 = document.createElement("source"); 
                            sourceMP4.type = "video/mp4";
                            sourceMP4.src = response.stream_link;
                            videlem.appendChild(sourceMP4);
                            // deactivate for public release
                            // var mpv_button = document.createElement("a");
                            // mpv_button.innerText = "MPV Streamen";
                            // var mpv_url = response.link.replace('https','mpv');
                            // mpv_button.setAttribute('href', mpv_url);
                            // mpv_button.classList.add('streammvp');
                            // episode.append(mpv_button);
                            episode.append(videlem);

                        } else {
                           console.warn(httpRequest.statusText, httpRequest.responseText);
                        }
                     });
                    httpRequest.responseType = 'json';
                    httpRequest.open('POST', '/premiumapi/resolvePath.php',true);
                    httpRequest.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                    httpRequest.send(encodeURIComponent("path")+"="+encodeURIComponent(path));
                }
            }
            
        }
    })
}

var createObserver = function() {
    var node = document.getElementsByTagName('body');
    if(node.length == 0) {
        window.setTimeout(createObserver,100);
    } else {
        mutationObserver.observe(
            node[0],    
            { attributes: true }
        )
    }
  
}
const mutationObserver = new MutationObserver(callback)

createObserver();
