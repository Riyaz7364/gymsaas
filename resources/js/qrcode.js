import QRCode from "qrcode";

window.generateQR = function (elementId, text) {
    const canvas = document.createElement("canvas");

    QRCode.toCanvas(
        canvas,
        text,
        {
            width: 256,
            margin: 2,
        },
        function (error) {
            if (error) console.error(error);

            const container = document.getElementById(elementId);
            container.innerHTML = ""; // clear old
            container.appendChild(canvas);
        },
    );
};
