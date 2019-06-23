var synth = window.speechSynthesis;
var voices = [];

function speakLastChessNotation(rawMoves, color)
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

    var speechHandler = new SpeechSynthesisUtterance(translated);
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
    return notation;
}

function extractLastMove(moves)
{
    var parts = moves.split("<br>");
    var lastPart = parts.slice(-1)[0];
    var rawNotationParts = lastPart.split("</b>");
    var rawNotation = rawNotationParts.slice(-1)[0];
    return rawNotation.trim();
}