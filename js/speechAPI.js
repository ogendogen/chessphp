var synth = window.speechSynthesis;
var voices = [];

function speakLastChessNotation(rawMoves)
{
    var lastMove = extractLastMove(rawMoves);
    var translated = transalateChessNotation(lastMove);
    speak(translated);
}

function speak(input)
{
    if (synth.speaking)
    {
        console.error("TTS already speaking");
        return;
    }

    var speechHandler = new SpeechSynthesisUtterance(input);
    var voices = synth.getVoices();
    if (voices.length === 0)
    {
        console.error("No voices found");
        return;
    }
    speechHandler.voice = voices[0];
    speechHandler.pitch = 1;
    speechHandler.rate = 1;
    synth.speak(speechHandler);
}

function populateVoices()
{
    voices = synth.getVoices();
}
populateVoices();
if (speechSynthesis.onvoiceschanged !== undefined)
{
    speechSynthesis.onvoiceschanged = populateVoices();
}

function transalateChessNotation(notation)
{
    var parts = notation.split(" ");
    var current = getLastArrayElement(parts);
    var color = (parts.length === 2 ? "Kolor czarny," : "Kolor biały,");
    var notation = getSingleNotation(notation);
    var specialMove = getSpecialMove(notation, color);

    var final;
    if (specialMove === "none")
    {
        var isCheck = getCheck(notation);
        var isBeaten = getBeat(notation);
        var figure = getFigure(notation);
        var sourcePosition = getSourcePosition(notation).toUpperCase();
        var targetPosition = getTargetPosition(notation).toUpperCase();

        if (isCheck && isBeaten)
        {
            final = color + " " + figure + " przemieszczenie z pola " + sourcePosition + " na pole " + targetPosition.slice(0, -1) + ", zbicie figury, szach";
        }
        else if (isCheck)
        {
            final = color + " " + figure + " przemieszczenie z pola " + sourcePosition + " na pole " + targetPosition.slice(0, -1) + ", szach";
        }
        else if (isBeaten)
        {
            final = color + " " + figure + " przemieszczenie z pola " + sourcePosition + " na pole " + targetPosition + " i zbicie figury";
        }
        else
        {
            final = color + " " + figure + " przemieszczenie z pola " + sourcePosition + " na pole " + targetPosition;
        }
    }
    else
    {
        final = specialMove;
    }

    return final;
}

function getBeat(notation)
{
    return notation.includes(":");
}

function getCheck(notation)
{
    return notation.includes("+");
}

function getSpecialMove(notation, color)
{
    if (notation === "O-O")
    {
        return color + "krótka roszada";
    }
    else if (notation === "O-O-O")
    {
        return color + "długa roszada";
    }
    else if (notation === "1-0")
    {
        return "Kolor biały wygrywa!";
    }
    else if (notation === "0-1")
    {
        return "Kolor czarny wygrywa!";
    }
    else if (notation === "0.5-0.5")
    {
        return "Remis.";
    }
    else if (notation.includes("#"))
    {
        return "Szach mat!";
    }

    return "none";
}

function getSourcePosition(notation)
{
    if (notation[0] == notation[0].toUpperCase())
    {
        notation = notation.substring(1);
    }
    if (notation.includes("-"))
    {
        return notation.split("-")[0];
    }
    else if (notation.includes(":"))
    {
        return notation.split(":")[0];
    }
}

function getTargetPosition(notation)
{
    if (notation.includes("-"))
    {
        return notation.split("-")[1];
    }
    else if (notation.includes(":"))
    {
        return notation.split(":")[1];
    }
}

function getFigure(notation)
{
    var symbol = notation[0];

    if (symbol === symbol.toLowerCase())
    {
        return "pion";
    }

    switch(symbol)
    {
        case "K":
        {
            return "król";
        }
        case "H":
        {
            return "królowa";
        }
        case "G":
        {
            return "goniec";
        }
        case "S":
        {
            return "skoczek";
        }
        case "W":
        {
            return "wieża";
        }
        default:
        {
            return "nieznana figura";
        }
    }
}

function extractLastMove(moves)
{
    var parts = moves.split("<br>");
    var lastPart = getLastArrayElement(parts);
    var rawNotationParts = lastPart.split("</b>");
    var rawNotation = getLastArrayElement(rawNotationParts);
    return rawNotation.trim();
}

function getLastArrayElement(array)
{
    return array.slice(-1)[0];
}

function getSingleNotation(notation)
{
    return notation.includes(" ") ? getLastArrayElement(notation.split(" ")) : notation;
}