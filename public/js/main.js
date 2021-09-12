const shootingBoard = document.querySelector('#shootingBoard');

shootingBoard.addEventListener('click', (event) => {
    const cell = event.target;
    const x = cell.dataset.x;
    const y = cell.dataset.y;

    if (x === undefined || y === undefined) {
        return;
    }

    const isCellEmpty = cell.classList.contains('empty');

    if (isCellEmpty) {
        window.location = window.location.origin + "?x=" + x + "&y=" + y;
    }
});
