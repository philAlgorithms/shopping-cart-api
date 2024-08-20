const $initialState = {
    pdfDoc: null,
    currentPage: 1,
    pageCount: 0,
    zoom: 1.2,
};

// Render the page.
const renderPage = () => {
    // Load the first page.
    $initialState.pdfDoc
        .getPage($initialState.currentPage)
        .then((page) => {
            const canvas = $('#document-canvas')[0];
            const $ctx = canvas.getContext('2d');
            const $viewport = page.getViewport({
                scale: $initialState.zoom,
            });

            const resolution = 2;

            canvas.height = resolution * $viewport.height;
            canvas.width = resolution * $viewport.width;
            $ctx.clearRect(0, 0, canvas.width, canvas.height);
            // Render the PDF page into the canvas context.
            const renderCtx = {
                canvasContext: $ctx,
                viewport: $viewport,
                transform: [resolution, 0, 0, resolution, 0, 0]
            };

            page.render(renderCtx);

            $('#page_num').html($initialState.currentPage);
            $('#current_page').val($initialState.currentPage);
        });
};

// Load the document.
const loadPdf = ($pdf, options) => {
    const success = options.success === undefined ? () => {} : options.success;
    const error = options.error === undefined ? () => {} : options.error;
    pdfjsLib
        .getDocument($pdf)
        .promise.then((doc) => {
            $initialState.pdfDoc = doc;
            $('#page_count').html($initialState.pdfDoc.numPages);
            if ($initialState.pdfDoc.numPages < $initialState.currentPage) {
                $initialState.currentPage = 1;
            }
            renderPage();

            success();
        })
        .catch((err) => {
            error();
        });
}

const getImageFromPdf = ($pdf, $pageNumber) => {
    // Load the first page.
    const $image = null;
    if ($pageNumber === undefined) { $pageNumber = 1; }
    return pdfjsLib.getDocument($pdf)
        .promise.then((doc) => {
            return doc.getPage($pageNumber)
                .then((page) => {
                    const canvas = document.createElement('canvas');
                    const $ctx = canvas.getContext('2d');

                    const $viewport = page.getViewport({
                        scale: 1
                    });

                    canvas.height = $viewport.height;
                    canvas.width = $viewport.width;
                    $ctx.clearRect(0, 0, canvas.width, canvas.height);

                    const renderCtx = {
                        canvasContext: $ctx,
                        viewport: $viewport,
                    };

                    page.render(renderCtx);
                    var prom = new Promise(function (resolve, reset) {
                        window.setTimeout(function () {
                            const res = canvas.toDataURL('image/png');
                            resolve(res);
                        }, 2000);
                    });
                    return prom;

                });
        });
};


function showPrevPage() {
    if ($initialState.pdfDoc === null || $initialState.currentPage <= 1)
        return;
    $initialState.currentPage--;
    // Render the current page
    $('#current_page').val($initialState.currentPage);
    renderPage();
}

function showNextPage() {
    if (
        $initialState.pdfDoc === null ||
        $initialState.currentPage >=
        $initialState.pdfDoc._pdfInfo.numPages
    )
        return;

    $initialState.currentPage++;
    $('#current_page').val($initialState.currentPage);
    renderPage();
}

// Button events.
$('#prev-page').click(showPrevPage);
$('#next-page').click(showNextPage);

// Display a specific page.
$('#current_page').on('keypress', (event) => {
    if ($initialState.pdfDoc === null) return;
    // Get the key code.
    const $keycode = event.keyCode ? event.keyCode : event.which;
    if ($keycode === 13) {
        // Get the new page number and render it.
        let desiredPage = $('#current_page')[0].valueAsNumber;

        $initialState.currentPage = Math.min(
            Math.max(desiredPage, 1),
            $initialState.pdfDoc._pdfInfo.numPages,
        );
        renderPage();

        $('#current_page').val($initialState.currentPage);
    }
});

// Zoom functionality.
$('#zoom_in').on('click', () => {
    if ($initialState.pdfDoc === null) return;
    $initialState.zoom *= 4 / 3;

    renderPage();
});

$('#zoom_out').on('click', () => {
    if ($initialState.pdfDoc === null) return;
    $initialState.zoom *= 2 / 3;
    renderPage();
});

$(".view-document").click(function () {
    $pdf = $(this).attr('data-doc-path');
    loadPdf($pdf);
});