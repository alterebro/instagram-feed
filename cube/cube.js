const Store = {
    // -------
    // Data
    state : {
        instagramFeed : [],
        instagramQuery : 'alterebro',
        instagramQueryType : null,
        instagramQueryTypes : [
            {type : 'Username', symbol: '@'},
            {type : 'Hashtag', symbol: '#'}
        ],

        cubeSides : [
            { side : 'left', label: '&#x31;', x : 0, y: 90 },
            { side : 'back', label: '&#x36;', x : 0, y: -180 },
            { side : 'top', label: '&#x33;', x : -90, y: 0 },
            { side : 'bottom', label: '&#x34;', x : 90, y: 0 },
            { side : 'front', label: '&#x35;', x : 0, y: 0 },
            { side : 'right', label: '&#x32;', x : 0, y: -90 },
        ],
        cubeRotation : {
            transform: `translateZ(-2400px) rotateX(0deg) rotateY(0deg)`,
        },
        cubeRotate : true,
        cubeRotateId : null,
        cubeRotateMs : 1500,
        cubeSize : 300
    },

    // -------
    // Methods
    changeSide : function(side, nonStop) {
        if ( !nonStop ) {
            console.log(this);
            this.state.cubeRotate = false;
            this.state.cubeRotateId = null;
        }
        let x = side.x + ((Math.random()*40) - 20);
        let y = side.y + ((Math.random()*40) - 20);
        this.state.cubeRotation.transform = `translateZ(-${this.state.cubeSize/2}px) rotateX(${x}deg) rotateY(${y}deg)`;
    },
    autoRotate : function() {
        if ( this.state.cubeRotate ) {
            let _randomSide = Math.floor(Math.random()*this.state.cubeSides.length);
            this.changeSide(this.state.cubeSides[_randomSide], true);
            this.state.cubeRotateId = window.setTimeout( () => {
                Store.autoRotate()
            }, this.state.cubeRotateMs);
        }
    },
    setCubeSize : function() {
        document.documentElement.style.setProperty('--cube-size', `${Store.state.cubeSize}px`);
    }
}

// ----------
// Components
const CubeHead = {
    data: function () { return Store.state },
    name : "CubeHead",
    methods : {
        requestFeed : function() {
            console.log(
                'Requesting Feed!',
                this.instagramQueryType.type.toLowerCase(),
                ':', this.instagramQuery
            );
        }
    },
    created() {
        Store.state.instagramQueryType = Store.state.instagramQueryTypes[0];
    }
}
const Cube = {
    data: function () { return Store.state },
    name : "Cube"
}
const CubeFoot = {
    data: function () { return Store.state },
    name : "CubeFoot",
    methods : {
        changeSide(side) {
            Store.changeSide(side, false);
        },
        stopRotation() {
            Store.state.cubeRotate = false;
        },
        resumeRotation() {
            Store.state.cubeRotate = true;
            Store.autoRotate();
        },
        zoomIn() {
            Store.state.cubeSize += 30;
            Store.setCubeSize();
        },
        zoomOut() {
            Store.state.cubeSize -= 30;
            Store.setCubeSize();
        }
    }
}

// ----------
// Main App
const App = new Vue({
    el: '#app',
    components : {
        Cube,
        CubeFoot,
        CubeHead
    },
    created : function() {

        Store.setCubeSize();
        let _xhr = new XMLHttpRequest();
            _xhr.overrideMimeType('application/json');
            _xhr.open('GET', 'feed.php', true);
            _xhr.onreadystatechange = function() {
                if (_xhr.readyState === 4 && _xhr.status == "200") {
                    Store.state.instagramFeed = JSON.parse(_xhr.responseText).slice(0,6);
                    Store.autoRotate();
                } else {
                    Store.state.instagramFeed = [];
                }
            }
        _xhr.send();
    }
});
