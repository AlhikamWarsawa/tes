const themeSlug =  "hoster---hosting-website----com";
function clearForm(formId) {
  document.getElementById(formId).reset();
}
 window.addEventListener("message", (event) => {
    if (event?.data?.zoom){
        const mains = document.getElementsByTagName('main')
        for (let main of mains) {
            main.style.zoom = event?.data?.zoom
        }
    }
}, false);



    document.addEventListener('DOMContentLoaded', () => {
        function applyZoom() {
            const vpTags = document.getElementsByClassName('yotako-main');
                let closest;
                let parentElement;
                for (let vp of vpTags) {
                    if (vp.offsetParent) {
                        vp.classList.forEach(c => {
                            if (c.includes('size_')) {
                                parentElement = vp.parentElement;
                                closest = c.split('_')[1];
                            }
                        });
                    }
                }

                const zoom = window.innerWidth / closest;
                if (parentElement) {
                    parentElement.style.zoom = isNaN(zoom) ? 1 : zoom;
                }
            }
            window.onresize = function () {
                applyZoom();
            };
            setTimeout(() => {
                applyZoom();
            }, 500);
            
        });






