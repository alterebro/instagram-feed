const Data = {
    instagramFeed : [],
    cubeSides : [
        { side : 'front', x : 0, y: 0 },
        { side : 'right', x : 0, y: -90 },
        { side : 'back', x : 0, y: -180 },
        { side : 'left', x : 0, y: 90 },
        { side : 'top', x : -90, y: 0 },
        { side : 'bottom', x : 90, y: 0 },
    ],
    cubeRotation : {
        transform: `translateZ(-2400px) rotateX(0deg) rotateY(0deg)`,
    },
    cubeRotate : true,
    cubeRotateId : null,
    cubeSize : 300
}

const Cube = {
    data: function () { return Data },
    name : "Cube",
    template: '#cube',
    methods : {
        changeSide(side, nonStop = true) {
            if ( !nonStop ) {
                Data.cubeRotate = false;
                Data.cubeRotateId = null;
            }
            let x = side.x + ((Math.random()*40) - 20);
            let y = side.y + ((Math.random()*40) - 20);
            // let x = side.x;
            // let y = side.y;
            Data.cubeRotation.transform = `translateZ(-${Data.cubeSize/2}px) rotateX(${x}deg) rotateY(${y}deg)`;
        },
        autoRotate() {
            if ( Data.cubeRotate ) {
                let _randomSide = Math.floor(Math.random()*Data.cubeSides.length);
                this.changeSide(Data.cubeSides[_randomSide], true);
                Data.cubeRotateId = window.setTimeout(this.autoRotate, 1500)
            }
        }
    },
    created : function() {

        // Set cubeSize CSS Variable
        document.documentElement.style.setProperty('--cube-size', `${Data.cubeSize}px`);

        let _autoRotation = this.autoRotate;
        let _xhr = new XMLHttpRequest();
            _xhr.overrideMimeType('application/json');
            _xhr.open('GET', 'feed.php', true);
            _xhr.onreadystatechange = function() {
                if (_xhr.readyState === 4 && _xhr.status == "200") {
                    Data.instagramFeed = JSON.parse(_xhr.responseText).slice(0,6);
                    _autoRotation();
                } else {
                    Data.instagramFeed = [];
                }
            }
        _xhr.send();
    }
};

const App = new Vue({
    el: '#app',
    components : { Cube }
});
