const Store = {
    // -------
    // Consttants
    feedURL : 'feed.php',

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
        cubeSideCurrent : null,
        cubeRotation : {
            transform: `translateZ(-2400px) rotateX(0deg) rotateY(0deg)`,
        },
        cubeRotate : true,
        cubeRotateId : null,
        cubeRotateMs : 1500,
        cubeRotationSwing : 25,
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
        let x = side.x + ((Math.random()*(this.state.cubeRotationSwing*2)) - this.state.cubeRotationSwing);
        let y = side.y + ((Math.random()*(this.state.cubeRotationSwing*2)) - this.state.cubeRotationSwing);
        this.state.cubeRotation.transform = `translateZ(-${this.state.cubeSize/2}px) rotateX(${x}deg) rotateY(${y}deg)`;
        this.state.cubeSideCurrent = side;
    },
    autoRotate : function() {
        if ( this.state.cubeRotate ) {
            let _randomSide = Math.floor(Math.random()*this.state.cubeSides.length);
            this.changeSide(this.state.cubeSides[_randomSide], true);
            this.state.cubeRotateId = window.setTimeout( () => {
                this.autoRotate()
            }, this.state.cubeRotateMs);
        }
    },
    setCubeSize : function() {
        document.documentElement.style.setProperty('--cube-size', `${this.state.cubeSize}px`);
    },
    getInstagramFeed : function() {
        let _xhr = new XMLHttpRequest();
            _xhr.overrideMimeType('application/json');
            _xhr.open('GET', 'feed.php', true);
            _xhr.onreadystatechange = () => {
                if (_xhr.readyState === 4 && _xhr.status == "200") {
                    this.state.instagramFeed = JSON.parse(_xhr.responseText).slice(0,6);
                    this.autoRotate();
                } else {
                    this.state.instagramFeed = [];
                }
            }
        _xhr.send();
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
        },
        lockCubeRotation() {
            Store.state.cubeRotationSwing = 0;
            Store.changeSide(Store.state.cubeSideCurrent, true);
        },
        unlockCubeRotation() {
            Store.state.cubeRotationSwing = 25;
            Store.changeSide(Store.state.cubeSideCurrent, true);
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
        Store.state.cubeSideCurrent = Store.state.cubeSides[0];
        Store.setCubeSize();
        Store.getInstagramFeed();
    }
});
